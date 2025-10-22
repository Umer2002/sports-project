<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use DOMDocument;
use Yajra\DataTables\DataTables;

class NewsController extends Controller
{
    public function index()
    {
        $newsItems = News::all();
        return view('admin.news.index', compact('newsItems'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'category' => 'nullable|string|max:100',
        'content' => 'string',
        'image' => 'image|max:2048',
    ]);

    $content = $request->get('content');
    $finalContent = null;

    if (!empty($content)) {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHtml($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach ($dom->getElementsByTagName('img') as $img) {
            $src = $img->getAttribute('src');
            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mime = $groups['mime'];
                $filename = uniqid() . ".$mime";
                $path = "uploads/news/$filename";
                \Image::make($src)->encode($mime, 100)->save(public_path($path));
                $img->setAttribute('src', asset($path));
            }
        }

        $finalContent = $dom->saveHTML();
    }

    $news = new News($request->except('image', 'content'));
    $news->content = $finalContent;

    if ($request->hasFile('image')) {
        $filename = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(public_path('uploads/news'), $filename);
        $news->image = $filename;
    }

    $news->save();
    return redirect()->route('admin.news.index')->with('success', 'News created successfully.');
}


    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHtml($request->get('content'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach ($dom->getElementsByTagName('img') as $img) {
            $src = $img->getAttribute('src');
            if (preg_match('/data:image/', $src)) {
                preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
                $mime = $groups['mime'];
                $filename = uniqid() . ".$mime";
                $path = "uploads/news/$filename";
                Image::make($src)->encode($mime, 100)->save(public_path($path));
                $img->setAttribute('src', asset($path));
            }
        }

        $news->content = $dom->saveHTML();

        if ($request->hasFile('image')) {
            $filename = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('uploads/news'), $filename);
            $news->image = $filename;
        }

        $news->update($request->except('content', 'image'));
        return redirect()->route('admin.news.index')->with('success', 'News updated successfully.');
    }

    public function destroy(News $news)
    {
        $news->delete();
        return response()->json(['status' => 'success']);
    }

    public function confirmDelete(News $news)
    {
        $model = 'news';
        $confirm_route = route('admin.news.delete', ['id' => $news->id]);
        return view('admin.layouts.modal_confirmation', compact('model', 'confirm_route'));
    }

    public function data()
    {
        $news = News::select(['id', 'title', 'category', 'created_at']);

        return DataTables::of($news)
            ->editColumn('created_at', fn ($row) => $row->created_at->diffForHumans())
            ->addColumn('actions', function ($news) {
                return view('admin.news.partials.actions', compact('news'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
