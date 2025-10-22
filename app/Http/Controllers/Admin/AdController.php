<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ads = Ad::all();
        return view('admin.ads.index', compact('ads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'link' => 'nullable|string|max:255',
        ];

        $mediaRule = 'required|file|mimes:jpg,jpeg,png,gif,webp|max:2048';
        if ($request->input('type') === 'video') {
            $mediaRule = 'required|file|mimes:mp4,mov,wmv,avi,mkv,webm,m4v|max:51200';
        }
        $rules['media'] = $mediaRule;

        $data = $request->validate($rules);
        $data['active'] = $request->has('active');

        $disk = 'public';
        $directory = $data['type'] === 'image' ? 'ads/images' : 'ads/videos';
        $data['media'] = $request->file('media')->store($directory, $disk);

        Ad::create($data);
        return redirect()->route('admin.ads.index')->with('success', 'Ad created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'link' => 'nullable|string|max:255',
        ];

        $requiresUpload = !$ad->media || $request->input('type') !== $ad->type;
        $mediaRules = [$requiresUpload ? 'required' : 'nullable', 'file'];
        if ($request->input('type') === 'video') {
            $mediaRules[] = 'mimes:mp4,mov,wmv,avi,mkv,webm,m4v';
            $mediaRules[] = 'max:51200';
        } else {
            $mediaRules[] = 'mimes:jpg,jpeg,png,gif,webp';
            $mediaRules[] = 'max:2048';
        }

        $rules['media'] = $mediaRules;

        $data = $request->validate($rules);
        $data['active'] = $request->has('active') ? 1 : 0;

        if ($request->hasFile('media')) {
            if ($ad->media && Storage::disk('public')->exists($ad->media)) {
                Storage::disk('public')->delete($ad->media);
            }
            $directory = $data['type'] === 'image' ? 'ads/images' : 'ads/videos';
            $data['media'] = $request->file('media')->store($directory, 'public');
        }

        $ad->update($data);
        return redirect()->route('admin.ads.index')->with('success', 'Ad updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ad $ad)
    {
        $ad->delete();
        return redirect()->route('admin.ads.index')->with('success', 'Ad deleted successfully.');
    }
}
