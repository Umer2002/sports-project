<?php

namespace App\Http\Controllers;
use App\Models\FileUploader;
use App\Models\Player;
use App\Models\Posts;
use App\Models\Saveforlater;
use App\Models\User;
use App\Models\Video;
//Used for Form data validation
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Session;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\VideoComment;
use App\Models\VideoLike;

class VideoController extends Controller
{
    public function index()
    {
        return redirect()->route('player.videos.explore');
    }

    public function videos()
    {
        $page_data['vidoes'] = Video::where('category', 'video')->where('privacy', 'public')->orderBy('id', 'DESC')->limit(5)->get();
        $page_data['view_path'] = 'admin.video-shorts.video';
        return view('admin.video-shorts.video', $page_data);
    }

    // React index page
    public function reactIndex(Request $request)
    {
        $user = $request->user();
        $player = Player::with(['teams.coaches', 'ads'])->where('user_id', $user->id)->first();
        $allowLive = $user->coach()->exists()
            || $user->hasRole('coach')
            || $user->hasRole('club');
        $myVideos = Video::withCount(['likes', 'comments'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(12)
            ->get();

        $communityVideos = collect();
        $userIds = collect([$user->id]);
        if ($player) {
            $teamIds = $player->teams->pluck('id');
            $teammateIds = User::whereHas('player.teams', function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            })->pluck('id');
            $coachIds = User::whereHas('coach.teams', function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            })->pluck('id');
            $userIds = $userIds->merge($teammateIds)->merge($coachIds)->unique();
        }

        if ($userIds->isNotEmpty()) {
            $communityVideos = Video::withCount(['likes', 'comments'])
                ->whereIn('user_id', $userIds)
                ->latest()
                ->take(9)
                ->get();
        }

        $allInitialIds = $myVideos->pluck('id')->merge($communityVideos->pluck('id'))->unique();
        $initialLikedIds = $allInitialIds->isEmpty()
            ? collect()
            : VideoLike::where('user_id', $user->id)
                ->whereIn('video_id', $allInitialIds)
                ->pluck('video_id')
                ->flip();

        $mapVideo = function (Video $video) use ($initialLikedIds) {
            $previewUrl = $video->playback_url ?: $video->url;
            $isLive = Str::endsWith(strtolower($previewUrl ?? ''), '.m3u8') || strtolower((string) $video->video_type) === 'live';
            $showUrl = route('player.videos.show', $video->id);

            return [
                'id' => $video->id,
                'title' => $video->title,
                'description' => $video->description,
                'category' => $video->category,
                'video_type' => $video->video_type,
                'url' => $previewUrl,
                'playback_url' => $video->playback_url,
                'is_live' => $isLive,
                'likes_count' => (int) ($video->likes_count ?? 0),
                'comments_count' => (int) ($video->comments_count ?? 0),
                'created_at' => optional($video->created_at)->toIso8601String(),
                'created_at_human' => optional($video->created_at)->diffForHumans(),
                'is_liked' => $initialLikedIds->has($video->id),
                'like_url' => route('player.videos.like', $video->id),
                'unlike_url' => route('player.videos.unlike', $video->id),
                'comment_url' => route('player.videos.comment', $video->id),
                'comments_url' => route('player.videos.comments.index', $video->id),
                'share_url' => $showUrl,
                'show_url' => $showUrl,
            ];
        };

        $layout = $user->hasRole('referee') ? 'layouts.referee-dashboard' : 'layouts.player-new';

        return view('players.react-videos', [
            'user' => $user,
            'player' => $player,
            'allowLive' => $allowLive,
            'initialMyVideos' => $myVideos->map($mapVideo)->values(),
            'initialCommunityVideos' => $communityVideos->map($mapVideo)->values(),
            'feedEndpoint' => route('player.videos.feed-json'),
            'showBaseUrl' => url('/player/videos/explore'),
            'layout' => $layout,
        ]);
    }

    public function show(Request $request, Video $video)
    {
        $user = $request->user();
        $player = Player::with(['teams.coaches', 'ads'])->where('user_id', $user->id)->first();
        $allowLive = $user->coach()->exists()
            || $user->hasRole('coach')
            || $user->hasRole('club');

        $userIds = collect([$user->id]);
        if ($player) {
            $teamIds = $player->teams->pluck('id');
            $teammateIds = User::whereHas('player.teams', function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            })->pluck('id');
            $coachIds = User::whereHas('coach.teams', function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            })->pluck('id');
            $userIds = $userIds->merge($teammateIds)->merge($coachIds)->unique();
        }

        $ownsVideo = $video->user_id && (int) $video->user_id === (int) $user->id;
        $canBypassNetwork = $ownsVideo
            || $user->hasRole('club')
            || $user->hasRole('coach')
            || $user->hasRole('referee')
            || $user->hasRole('player')
            || ($user->is_admin ?? false);

        if ($video->user_id && ! $canBypassNetwork && ! $userIds->contains($video->user_id) && ! $video->is_ad) {
            abort(403, 'This video is outside your network.');
        }

        $video->load([
            'user:id,name',
            'user.player:id,user_id,photo',
            'user.player.team',
            'user.player.teams',
            'comments' => function ($query) {
                $query->latest()->with([
                    'user:id,name',
                    'user.player:id,user_id,photo',
                ]);
            },
        ])->loadCount(['likes', 'comments']);

        $previewUrl = $video->playback_url ?: $video->url;
        $isLive = Str::endsWith(strtolower($previewUrl ?? ''), '.m3u8') || strtolower((string) $video->video_type) === 'live';
        $teamName = data_get($video, 'user.player.team.name') ?? data_get($video, 'user.player.teams.0.name');
        $isLiked = VideoLike::where('video_id', $video->id)
            ->where('user_id', $user->id)
            ->exists();

        $videoData = [
            'id' => $video->id,
            'title' => $video->title,
            'description' => $video->description,
            'category' => $video->category,
            'video_type' => $video->video_type,
            'url' => $previewUrl,
            'playback_url' => $video->playback_url,
            'is_live' => $isLive,
            'created_at_human' => optional($video->created_at)->diffForHumans(),
            'created_at_exact' => optional($video->created_at)->timezone(config('app.timezone'))->format('M j, Y • g:i A'),
            'likes_count' => (int) $video->likes_count,
            'comments_count' => (int) $video->comments_count,
            'is_liked' => $isLiked,
            'team_name' => $teamName,
            'privacy' => $video->privacy ?? 'public',
            'author' => [
                'name' => $video->user->name ?? 'Teammate',
                'photo' => optional($video->user->player)->photo ? asset('storage/players/' . $video->user->player->photo) : null,
                'initials' => Str::of($video->user->name ?? 'T')->substr(0, 2)->upper(),
            ],
            'comments' => $video->comments->map(function (VideoComment $comment) {
                $name = $comment->user?->name ?? $comment->author_name ?? 'Teammate';
                return [
                    'id' => $comment->id,
                    'name' => $name,
                    'initials' => Str::of($name)->substr(0, 2)->upper(),
                    'avatar' => optional($comment->user?->player)->photo ? asset('storage/players/' . $comment->user->player->photo) : $comment->author_avatar,
                    'content' => $comment->content,
                    'created_at' => optional($comment->created_at)->diffForHumans(),
                    'created_at_exact' => optional($comment->created_at)->format('M j, Y g:i A'),
                ];
            })->values(),
        ];

        $relatedQuery = Video::query()
            ->where('id', '!=', $video->id)
            ->withCount(['likes', 'comments'])
            ->latest();

        if ($userIds->isNotEmpty()) {
            $relatedQuery->whereIn('user_id', $userIds);
        }

        $relatedVideos = $relatedQuery
            ->take(6)
            ->get();

        $relatedVideosData = $relatedVideos->map(function (Video $related) {
            $preview = $related->playback_url ?: $related->url;
            $isLiveRelated = Str::endsWith(strtolower($preview ?? ''), '.m3u8') || strtolower((string) $related->video_type) === 'live';
            return [
                'id' => $related->id,
                'title' => $related->title,
                'category' => $related->category,
                'video_type' => $related->video_type,
                'url' => $preview,
                'playback_url' => $related->playback_url,
                'is_live' => $isLiveRelated,
                'created_at_human' => optional($related->created_at)->diffForHumans(),
            ];
        })->values();

        $layout = $user->hasRole('referee') ? 'layouts.referee-dashboard' : 'layouts.player-new';

        return view('players.video-show', [
            'videoData' => $videoData,
            'relatedVideosData' => $relatedVideosData,
            'endpoints' => [
                'like' => route('player.videos.like', $video),
                'unlike' => route('player.videos.unlike', $video),
                'comment' => route('player.videos.comment', $video),
                'comments' => route('player.videos.comments.index', $video),
                'share' => route('player.videos.show', $video),
                'back' => route('player.videos.explore'),
            ],
            'layout' => $layout,
        ]);
    }



    public function store(Request $request)
    {
        dd($request->all());
        $rules = array('video' => 'required|file|mimes:mp4,mov,wmv,mkv,webm,avi,m4v| max:500000');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }
        // dd($request->all());
        $file_name = FileUploader::upload($request->video,'public/storage/videos');

        $mobile_app_image = FileUploader::upload($request->mobile_app_image,'public/storage/videos');

        $video = new Video();
        $video->title = $request->title;
        $video->user_id = auth()->user()->id;
        $video->privacy = $request->privacy;
        $video->category = $request->category;
        $video->mobile_app_image = $mobile_app_image;
        $video->file = $file_name;
        $video->view = json_encode(array());
        $done = $video->save();
        if ($done) {
            $post = new Posts();
            $post->user_id = auth()->user()->id;
            $post->publisher = 'video_and_shorts';
            $post->publisher_id = $video->id;
            $post->post_type = $request->category;
            $post->privacy = $request->privacy;
            $post->description = $request->title;
            $post->mobile_app_image = $mobile_app_image;
            $post->tagged_user_ids = json_encode(array());
            $post->user_reacts = json_encode(array());
            $post->status = 'active';
            $post->created_at = time();
            $post->updated_at = time();
            $post->save();
        }
        Session::flash('success_message', get_phrase('Video/Shorts Created Successfully'));
        return json_encode(array('reload' => 1));
    }

    public function videoinfo($id){
        $page_data['post'] = Posts::where('posts.privacy', '!=', 'private')
        ->where('posts.publisher', 'video_and_shorts')
        ->where('posts.publisher_id', $id)
        ->where('posts.status', 'active')
        ->first();

        $video = Video::find($id);
        $page_data['video'] = $video;
        $video_view_data = json_decode($video->view);
        if (!in_array(auth()->user()->id, $video_view_data)){
            array_push($video_view_data, auth()->user()->id);
            $video->view =  json_encode($video_view_data);
            $video->save();
        }
        $page_data['letestvideos'] = Video::where('category', 'video')->where('privacy', 'public')->orderBy('id', 'DESC')->limit('5')->get();
        $last_data = Video::latest()->first();
        if($last_data->id == $id){
            $page_data['vidoes'] = Video::where('id','<',$id)->where('category', 'video')->where('privacy', 'public')->orderBy('id', 'DESC')->limit('2')->get();
        }else{
            $page_data['vidoes'] = Video::where('id','>',$id)->where('category', 'video')->where('privacy', 'public')->orderBy('id', 'ASC')->limit('2')->get();
        }
        $page_data['view_path'] = 'frontend.video-shorts.video-detail';
        return view('frontend.index', $page_data);
    }


    public function load_videos_by_scrolling(Request $request)
    {
        $vidoes =  Video::where('category', 'video')->where('privacy', 'public')->skip($request->offset)->take(5)->orderBy('id', 'DESC')->get();
        $page_data['vidoes'] = $vidoes;
        return view('frontend.video-shorts.single-video', $page_data);
    }



    public function shorts(){
        $page_data['shorts'] = Video::where('category', 'shorts')->where('privacy', 'public')->orderBy('id', 'DESC')->limit(5)->get();
        $page_data['view_path'] = 'frontend.video-shorts.shorts';
        return view('frontend.index', $page_data);
    }

    // JSON feed for React UI
    public function feedJson(Request $request)
    {
        $user = $request->user();
        // Build social circle: self + teammates + coaches
        $player = \App\Models\Player::with(['teams.coaches'])->where('user_id', $user->id)->first();
        $userIds = collect([$user->id]);
        if ($player) {
            $teamIds = $player->teams->pluck('id');
            $teammateIds = \App\Models\User::whereHas('player.teams', function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            })->pluck('id');
            $coachIds = \App\Models\User::whereHas('coach.teams', function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            })->pluck('id');
            $userIds = $userIds->merge($teammateIds)->merge($coachIds)->unique();
        }

        $limit = min(max((int) $request->query('limit', 12), 1), 50);
        $page = max((int) $request->query('page', 1), 1);
        $category = $request->query('category');
        $type = $request->query('type');
        $scope = $request->query('scope');
        $q = trim((string) $request->query('q', ''));

        $query = Video::latest()->withCount(['comments', 'likes']);
        if ($scope === 'mine') {
            $query->where('user_id', $user->id);
        } else {
            $query->whereIn('user_id', $userIds);
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($type) {
            $query->where('video_type', $type);
        }
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        $total = (clone $query)->count();
        $videos = $query->skip(($page - 1) * $limit)->take($limit)->get();

        $likedIds = $videos->isEmpty()
            ? collect()
            : VideoLike::where('user_id', $user->id)
                ->whereIn('video_id', $videos->pluck('id'))
                ->pluck('video_id')
                ->flip();

        $data = $videos->map(function($v) use ($likedIds) {
            $url = $v->playback_url ?: $v->url;
            $isLive = str_ends_with(strtolower($url), '.m3u8') || strtolower((string) $v->video_type) === 'live';
            $showUrl = route('player.videos.show', $v->id);

            return [
                'id' => $v->id,
                'title' => $v->title,
                'description' => $v->description,
                'category' => $v->category,
                'video_type' => $v->video_type,
                'url' => $url,
                'playback_url' => $v->playback_url,
                'is_live' => $isLive,
                'created_at' => optional($v->created_at)->toIso8601String(),
                'likes_count' => (int) $v->likes_count,
                'comments_count' => (int) $v->comments_count,
                'is_liked' => $likedIds->has($v->id),
                'like_url' => route('player.videos.like', $v->id),
                'unlike_url' => route('player.videos.unlike', $v->id),
                'comment_url' => route('player.videos.comment', $v->id),
                'comments_url' => route('player.videos.comments.index', $v->id),
                'share_url' => $showUrl,
                'show_url' => $showUrl,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => max((int) ceil($total / $limit), 1),
            ],
        ]);
    }


    public function load_shorts_by_scrolling(Request $request){
        $shorts =  Video::where('category', 'shorts')->where('privacy', 'public')->skip($request->offset)->take(5)->orderBy('id', 'DESC')->get();
        $page_data['shorts'] = $shorts;
        return view('frontend.video-shorts.shorts-single', $page_data);
    }


    public function save_for_later($id){
        $saveforlater = new Saveforlater();
        $saveforlater->user_id = auth()->user()->id;
        $saveforlater->video_id = $id;
        $saveforlater->save();
        Session::flash('success_message', get_phrase('Saved Successfully'));
        $response = array('reload' => 1);
        return json_encode($response);
    }


    public function unsave_for_later($id){
        $done = Saveforlater::where('video_id',$id)->where('user_id',auth()->user()->id)->delete();
        if($done){
        Session::flash('success_message', get_phrase('Unsaved Successfully'));
        $response = array('reload' => 1);
            return json_encode($response);
        }
    }


    public function save_all(){
        $page_data['videos'] = Saveforlater::where('user_id',auth()->user()->id)->whereNotNull('video_id')->whereNull('group_id')->whereNull('post_id')->whereNull('marketplace_id')->whereNull('event_id')->whereNull('blog_id')->get();
        $page_data['view_path'] = 'frontend.video-shorts.saved';
        return view('frontend.index', $page_data);
    }




    public function video_delete()
    {
        $response = array();
        $video = Video::find($_GET['video_id']);
        // store image name for delete file operation
        $file = $video->file;

        $done = $video->delete();
        if ($done) {
            $response = array('alertMessage' => get_phrase('Video Deleted Successfully'), 'fadeOutElem' => "#video-" . $_GET['video_id']);
            // just put the file name and folder name nothing more :)
            removeFile('video', $file);
        }
        return json_encode($response);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', Rule::in(['skill', 'tutorial', 'challenge', 'live'])],
            // Use mimes for better cross-browser compatibility (include m4v)
            'video' => ['required', 'file', 'mimes:mp4,mov,wmv,avi,mkv,webm,m4v'],
        ]);

        try {
            // Choose disk from env (default to 'public'). For GCS via S3 compatibility, set VIDEO_DISK=s3
            $disk = config('filesystems.default');
            $disk = env('VIDEO_DISK', $disk);
            $path = $request->file('video')->store('videos', $disk);

            // Map UI category to a safe, allowed enum for video_type
            // Potential values depending on migrations: training, match, tutorial, skill
            $cat = $request->category;
            if (in_array($cat, ['training', 'match', 'tutorial', 'skill'])) {
                $videoType = $cat;
            } elseif ($cat === 'challenge') {
                $videoType = 'skill';
            } elseif ($cat === 'live') {
                $videoType = 'training';
            } else {
                $videoType = 'tutorial';
            }

            try {
                $video = Video::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    // Store path; when rendering, use Storage::disk($disk)->url($path)
                    'url' => $path,
                    'video_type' => $videoType,
                    'category' => $cat,
                    'user_id' => $request->user()->id,
                ]);
            } catch (\Throwable $inner) {
                // Fallback if enum mismatch occurs (older schema uses 'information')
                \Log::warning('Retrying video create with fallback video_type', [
                    'original_error' => $inner->getMessage(),
                    'fallback' => 'information',
                ]);
                $video = Video::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'url' => $path,
                    'video_type' => 'information',
                    'category' => $cat,
                    'user_id' => $request->user()->id,
                ]);
            }

            // Seed a few AI-like starter comments (synthetic) to warm up engagement
            $names = [
                'Coach Miller', 'Alex P.', 'Jordan K.', 'Sam R.', 'Taylor V.',
                'Coach Kim', 'Riley S.', 'Chris D.', 'Morgan L.', 'Dana F.'
            ];
            $messages = [
                'Nice form! Keep the follow-through tight.',
                'Footwork looks sharp on this rep.',
                'Clean first touch — try accelerating into space next time.',
                'Great hustle. Mind your positioning off the ball.',
                'Love the angle on that pass. More like this.',
                'Tempo is improving — keep it consistent.',
                'Solid mechanics. Try a quicker release.',
                'Vision is there. Scan earlier to spot options.',
                'Power is good — aim for more control on the last touch.',
                'Crisp execution. Add variety to keep defenders guessing.'
            ];

            $seedCount = 3;
            for ($i = 0; $i < $seedCount; $i++) {
                VideoComment::create([
                    'video_id' => $video->id,
                    'user_id' => null,
                    'author_name' => $names[array_rand($names)],
                    'author_avatar' => null,
                    'content' => $messages[array_rand($messages)],
                ]);
            }

            return back()->with('status', 'Video uploaded successfully.');
        } catch (\Throwable $e) {
            // Log for debugging and show friendly error
            \Log::error('Video upload failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->withErrors(['video' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    public function comment(Request $request, Video $video)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment = VideoComment::create([
            'video_id' => $video->id,
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);
        $comment->load('user:id,name,photo');

        if ($request->expectsJson()) {
            $count = VideoComment::where('video_id', $video->id)->count();
            $name = $comment->user?->name ?? 'Teammate';
            return response()->json([
                'comment' => [
                    'id' => $comment->id,
                    'name' => $name,
                    'initials' => Str::of($name)->substr(0, 2)->upper(),
                    'avatar' => $comment->user && $comment->user->photo ? asset('storage/players/' . $comment->user->photo) : null,
                    'content' => $comment->content,
                    'created_at' => optional($comment->created_at)->diffForHumans(),
                    'created_at_exact' => optional($comment->created_at)->format('M j, Y g:i A'),
                ],
                'comments_count' => $count,
            ], 201);
        }

        return back()->with('status', 'Comment posted.');
    }

    public function comments(Request $request, Video $video)
    {
        $video->loadCount('comments');
        $limit = max(min((int) $request->query('limit', 40), 100), 1);
        $comments = $video->comments()
            ->latest()
            ->with('user:id,name,photo')
            ->take($limit)
            ->get()
            ->map(function (VideoComment $comment) {
                $name = $comment->user?->name ?? $comment->author_name ?? 'Teammate';
                return [
                    'id' => $comment->id,
                    'name' => $name,
                    'initials' => Str::of($name)->substr(0, 2)->upper(),
                    'avatar' => optional($comment->user?->player)->photo ? asset('storage/players/' . $comment->user->player->photo) : $comment->author_avatar,
                    'content' => $comment->content,
                    'created_at' => optional($comment->created_at)->diffForHumans(),
                    'created_at_exact' => optional($comment->created_at)->format('M j, Y g:i A'),
                ];
            })->values();

        return response()->json([
            'comments' => $comments,
            'comments_count' => (int) $video->comments_count,
        ]);
    }

    public function like(Request $request, Video $video)
    {
        VideoLike::firstOrCreate([
            'video_id' => $video->id,
            'user_id' => $request->user()->id,
        ]);
        if ($request->expectsJson()) {
            $count = VideoLike::where('video_id', $video->id)->count();
            return response()->json([
                'liked' => true,
                'likes_count' => $count,
            ]);
        }
        return back();
    }

    public function unlike(Request $request, Video $video)
    {
        VideoLike::where('video_id', $video->id)
            ->where('user_id', $request->user()->id)
            ->delete();
        if ($request->expectsJson()) {
            $count = VideoLike::where('video_id', $video->id)->count();
            return response()->json([
                'liked' => false,
                'likes_count' => $count,
            ]);
        }
        return back();
    }



}
