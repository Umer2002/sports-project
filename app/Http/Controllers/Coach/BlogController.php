<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DOMDocument;

class BlogController extends Controller
{
    public function index()
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        $blogs = Blog::where('user_id', auth()->id())
            ->with(['category', 'user'])
            ->latest()
            ->paginate(20);

        return view('coach.blog.index', compact('blogs'));
    }

    public function create()
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        $blogcategory = BlogCategory::pluck('title', 'id');
        $sports = Sport::pluck('name', 'id');

        return view('coach.blog.create', compact('blogcategory', 'sports'));
    }

    public function store(Request $request)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'is_public' => 'nullable|boolean',
            'visibility' => 'nullable|boolean',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,mov,qt,avi,wmv,mkv,webm,m4v|max:204800',
            'tags' => 'nullable|string',
        ]);

        $blog = new Blog();
        $blog->fill($validated);
        $blog->user_id = auth()->id();
        $blog->slug = Str::slug($request->title);
        $blog->content = $this->parseImages($request->content);
        
        // Handle visibility - use is_public if provided, otherwise use visibility
        if ($request->has('is_public')) {
            $blog->is_public = $request->boolean('is_public');
        } elseif ($request->has('visibility')) {
            $blog->is_public = $request->boolean('visibility');
        } else {
            $blog->is_public = true; // Default to public
        }

        if ($request->hasFile('image')) {
            $media = $request->file('image');
            $extension = strtolower($media->getClientOriginalExtension());
            $base = Str::slug($request->title) ?: Str::random(10);
            $filename = time() . '_' . $base . '.' . $extension;
            $destination = public_path('uploads/blog');

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $media->move($destination, $filename);
            $blog->image = $filename;
        }

        $blog->save();

        if ($request->filled('tags') && method_exists($blog, 'tag')) {
            $blog->tag($request->tags);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Blog created successfully.',
                'blog' => $blog->load(['user', 'category'])
            ]);
        }

        return redirect()->route('coach.blog.index')->with('success', 'Blog created successfully.');
    }

    public function show(Blog $blog)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Ensure the blog belongs to the current coach
        if ($blog->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this blog post.');
        }

        $comments = $blog->comments;
        return view('coach.blog.show', compact('blog', 'comments'));
    }

    public function edit(Blog $blog)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Ensure the blog belongs to the current coach
        if ($blog->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this blog post.');
        }

        $blogcategory = BlogCategory::pluck('title', 'id');
        $sports = Sport::pluck('name', 'id');

        return view('coach.blog.edit', compact('blog', 'blogcategory', 'sports'));
    }

    public function update(Request $request, Blog $blog)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Ensure the blog belongs to the current coach
        if ($blog->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this blog post.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'is_public' => 'required|boolean',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,mov,qt,avi,wmv,mkv,webm,m4v|max:204800',
            'tags' => 'nullable|string'
        ]);

        $blog->fill($request->only([
            'title', 'blog_category_id', 'is_public'
        ]));

        $blog->slug = Str::slug($request->title);
        $blog->content = $this->parseImages($request->content);

        if ($request->hasFile('image')) {
            $media = $request->file('image');
            $extension = strtolower($media->getClientOriginalExtension());
            $base = Str::slug($request->title) ?: Str::random(10);
            $filename = time() . '_' . $base . '.' . $extension;
            $destination = public_path('uploads/blog');

            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $media->move($destination, $filename);
            $blog->image = $filename;
        }

        if ($request->filled('tags') && method_exists($blog, 'retag')) {
            $blog->retag($request->tags);
        }

        $blog->save();

        return redirect()->route('coach.blog.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Ensure the blog belongs to the current coach
        if ($blog->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this blog post.');
        }

        $blog->delete();

        return redirect()->route('coach.blog.index')->with('success', 'Blog deleted successfully.');
    }

    private function parseImages($content)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            if (strpos($src, 'data:image') === 0) {
                $this->saveBase64Image($src, $img);
            }
        }

        return $dom->saveHTML();
    }

    private function saveBase64Image($base64, $imgElement)
    {
        $data = explode(',', $base64);
        $imageData = base64_decode($data[1]);
        $extension = 'png';
        if (strpos($data[0], 'jpeg') !== false) {
            $extension = 'jpg';
        } elseif (strpos($data[0], 'gif') !== false) {
            $extension = 'gif';
        }

        $filename = time() . '_' . Str::random(10) . '.' . $extension;
        $destination = public_path('uploads/blog');

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        file_put_contents($destination . '/' . $filename, $imageData);
        $imgElement->setAttribute('src', asset('uploads/blog/' . $filename));
    }

    public function toggleLike(Blog $blog)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if user already liked this blog
        $existingLike = $blog->likes()->where('user_id', auth()->id())->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $liked = false;
        } else {
            // Like
            $blog->likes()->create(['user_id' => auth()->id()]);
            $liked = true;
        }

        $likesCount = $blog->likes()->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount
        ]);
    }

    public function indexComments(Blog $blog)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comments = $blog->comments()
            ->with('user')
            ->latest()
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->content,
                    'name' => $comment->user->name ?? 'Anonymous',
                    'created_at' => $comment->created_at->diffForHumans(),
                    'created_at_exact' => $comment->created_at->format('M jS, Y g:i A')
                ];
            });

        return response()->json([
            'comments' => $comments,
            'comments_count' => $blog->comments()->count()
        ]);
    }

    public function storeComment(Request $request, Blog $blog)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'comment' => 'required|string|max:500'
        ]);

        $comment = $blog->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->comment
        ]);

        $comment->load('user');

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'comment' => $comment->content,
                'name' => $comment->user->name ?? 'Anonymous',
                'created_at' => $comment->created_at->diffForHumans(),
                'created_at_exact' => $comment->created_at->format('M jS, Y g:i A')
            ],
            'comments_count' => $blog->comments()->count()
        ]);
    }
}
