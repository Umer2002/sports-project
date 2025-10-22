<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use DOMDocument;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with(['category', 'user'])->get();
        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        $blogcategory = BlogCategory::pluck('title', 'id');
        $sports = Sport::pluck('name', 'id');
        $clubs = User::whereHas('roles', fn($q) => $q->where('role_id', 3))->pluck('name', 'id');

        return view('admin.blog.create', compact('blogcategory', 'sports', 'clubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'is_public' => 'required|boolean',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg,webp,mp4,mov,qt,avi,wmv,mkv,webm,m4v|max:204800',
            'tags' => 'nullable|string',

        ]);

        $blog = new Blog();
        $blog->fill($validated);
        $blog->user_id = auth()->id();
        $blog->slug = Str::slug($request->title);

        $blog->content = $this->parseImages($request->content);

        if ($request->hasFile('image')) {
            $media = $request->file('image');
            $extension = strtolower($media->getClientOriginalExtension());
            $base = Str::slug($request->title) ?: Str::random(10);
            $filename = time() . '_' . $base . '.' . $extension;
            $destination = public_path('uploads/blog');

            if (! is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $media->move($destination, $filename);
            $blog->image = $filename;
        }


        $blog->save();

        if ($request->filled('tags') && method_exists($blog, 'tag')) {
            $blog->tag($request->tags);
        }

        return redirect()->route('admin.blog.index')->with('success', 'Blog created successfully.');
    }

    public function edit(Blog $blog)
    {
        $blogcategory = BlogCategory::pluck('title', 'id');
        $sports = Sport::pluck('name', 'id');
        $clubs = User::whereHas('roles', fn($q) => $q->where('role_id', 3))->pluck('name', 'id');

        return view('admin.blog.edit', compact('blog', 'blogcategory', 'sports', 'clubs'));
    }

    public function update(Request $request, Blog $blog)
    {
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

        $blog->content = $this->parseImages($request->content);

        if ($request->hasFile('image')) {
            $media = $request->file('image');
            $extension = strtolower($media->getClientOriginalExtension());
            $base = Str::slug($request->title) ?: Str::random(10);
            $filename = time() . '_' . $base . '.' . $extension;
            $destination = public_path('uploads/blog');

            if (! is_dir($destination)) {
                mkdir($destination, 0755, true);
            }

            $media->move($destination, $filename);
            $blog->image = $filename;
        }


        if ($request->filled('tags') && method_exists($blog, 'retag')) {
            $blog->retag($request->tags);
        }

        $blog->save();

        return redirect()->route('admin.blog.index')->with('success', 'Blog updated successfully.');
    }

    public function show(Blog $blog)
    {
        $comments = $blog->comments;
        return view('admin.blog.show', compact('blog', 'comments'));
    }

    public function getModalDelete(Blog $blog)
    {
        return view('admin.layouts.modal_confirmation', [
            'model' => 'blog',
            'confirm_route' => route('admin.blog.destroy', $blog->id),
            'error' => null,
        ]);
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blog.index')->with('success', 'Blog deleted successfully.');
    }

    public function storeComment(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:500',
            'author' => 'nullable|string|max:191',
        ]);

        $comment = new BlogComment($validated);
        $comment->blog_id = $blog->id;
        $comment->save();

        return redirect()->route('admin.blog.show', $blog->id)->with('success', 'Comment added.');
    }

    /**
     * Helper: Parse base64 images from content.
     */
    private function parseImages($htmlContent)
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach ($dom->getElementsByTagName('img') as $img) {
            $src = $img->getAttribute('src');
            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?);/', $src, $groups);
                $mime = $groups['mime'];
                $filename = uniqid();
                $filepath = "uploads/blog/{$filename}.{$mime}";
                Image::make($src)->encode($mime, 100)->save(public_path($filepath));
                $img->setAttribute('src', asset($filepath));
            }
        }

        return $dom->saveHTML();
    }
}
