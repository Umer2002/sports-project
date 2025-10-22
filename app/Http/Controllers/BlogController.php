<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $userId = optional(auth()->user())->id;

        $blogs = Blog::with(['user.player.team', 'user.player.teams'])
            ->withCount(['likes', 'comments'])
            ->withCount(['likes as is_liked' => function (Builder $query) use ($userId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }])
            ->latest()
            ->take(30)
            ->get()
            ->map(function (Blog $blog) {
                $blog->setAttribute('is_liked', (bool) $blog->is_liked);
                return $blog;
            });

        $context = $request->query('context');
        $useClubLayout = $context === 'club' && optional(auth()->user())->hasRole('club');

        return view('players.blog.index', [
            'blogs' => $blogs,
            'blogLayout' => $useClubLayout ? 'layouts.club-dashboard' : null,
            'blogContext' => $context,
        ]);
    }

    public function show(Request $request, $post)
    {
        $userId = optional(auth()->user())->id;

        $blog = Blog::with([
                'user.player.team',
                'user.player.teams',
                'comments' => function ($query) {
                    $query->latest()->take(40);
                },
            ])
            ->withCount([
                'likes',
                'comments',
                'likes as is_liked' => function (Builder $query) use ($userId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->findOrFail($post);

        $blog->setAttribute('is_liked', (bool) $blog->is_liked);

        $commentFeed = $blog->comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'name' => $comment->name ?: 'Teammate',
                'comment' => $comment->comment,
                'created_at' => optional($comment->created_at)->diffForHumans(),
                'created_at_exact' => optional($comment->created_at)->format('M j, Y g:i A'),
            ];
        })->values();

        $recentPosts = Blog::query()
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'created_at']);

        $context = $request->query('context');
        $useClubLayout = $context === 'club' && optional(auth()->user())->hasRole('club');

        return view('players.blog.detail', [
            'blog' => $blog,
            'commentFeed' => $commentFeed,
            'recentPosts' => $recentPosts,
            'blogLayout' => $useClubLayout ? 'layouts.club-dashboard' : null,
            'blogContext' => $context,
        ]);
    }

    public function viewPage(Request $request)
    {
        $user = $request->user();

        $latestBlogs = Blog::query()
            ->when($user, function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_public', 1)
            ->latest()
            ->take(2)
            ->get();

        return view('players.blog.add-new', compact('latestBlogs'));
    }

    public function saveBlog(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        $validated = $request->validate([
            'title'         => 'required|string|max:191',
            'content'       => 'required|string',
            'feature_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,mp4,mov,qt,avi,wmv,mkv,webm,m4v|max:204800',
        ]);

        $blog = new Blog();
        $blog->title       = $request->title;
        $blog->slug        = Str::slug($request->title);
        $blog->content     = $request->content;
        $blog->blog_category_id = 1;
        $blog->is_public   = (int) $request->input('visibility', 1);
        $blog->user_id     = $userId;
        $blog->views       = 0;
        // $blog->clicks      = 0;

        $blogMediaType = null;

        if ($request->hasFile('feature_image')) {
            $media      = $request->file('feature_image');
            $mimeType   = $media->getMimeType();
            $extension  = strtolower($media->getClientOriginalExtension());
            $baseName   = $request->title ?: pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);
            $slug       = Str::slug($baseName) ?: 'upload';
            $fileName   = time() . '_' . $slug . '.' . $extension;
            $uploadPath = public_path('uploads/blog');

            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $media->move($uploadPath, $fileName);
            $blog->image = $fileName;
            $blogMediaType = Str::startsWith((string) $mimeType, 'video/') ? 'video' : 'image';
        }

        $blog->save();

        $blog->load(['user.player.team', 'user.player.teams']);

        $mediaUrl = $blog->image ? asset('uploads/blog/' . $blog->image) : null;
        $extension = $blog->image ? strtolower(pathinfo($blog->image, PATHINFO_EXTENSION)) : null;
        $mediaType = $extension && in_array($extension, ['mp4','mov','qt','avi','wmv','mkv','webm','m4v']) ? 'video' : 'image';

        $blog->setAttribute('media_url', $mediaUrl);
        $blog->setAttribute('image_url', $mediaUrl);
        $blog->setAttribute('media_type', $blogMediaType ?? $mediaType);
        $blog->setAttribute('user_photo_url', optional($blog->user)->photo ? asset('storage/players/' . $blog->user->photo) : null);
        $blog->setAttribute('show_url', route('player.blogs.show', $blog->id));
        $blog->setAttribute('created_at_iso', optional($blog->created_at)->toIso8601String());
        $blog->setAttribute('likes_count', 0);
        $blog->setAttribute('comments_count', 0);
        $blog->setAttribute('is_liked', false);

        return response()->json([
            'success' => true,
            'message' => 'Blog post saved successfully!',
            'blog' => $blog,
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file      = $request->file('upload');
        $fileName  = uniqid() . '.' . $file->getClientOriginalExtension();
        $uploadDir = public_path('uploads/blog');

        $file->move($uploadDir, $fileName);

        $url = asset('uploads/blog/' . $fileName);

        return response()->json([
            'uploaded' => true,
            'url'      => $url
        ]);
    }
}
