<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class PlaytubeSyncController extends Controller
{
    /**
     * Webhook endpoint called by PlayTube after a successful upload
     * to persist the video in Laravel's database.
     */
    public function storeFromPlaytube(Request $request)
    {
        $video = Video::create([
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'url'         => $request->input('path'),
            'video_type'  => $request->input('video_type', 'information'),
            'category'    => $request->boolean('is_ad') ? 'shorts' : 'video',
            'user_id'     => $request->input('user_id'),
            'playtube_id' => $request->input('video_id'),
            'is_ad'       => $request->boolean('is_ad'),
        ]);

        return response()->json($video, 201);
    }

    /**
     * Handle a video uploaded via Laravel and push it to PlayTube.
     */
    public function storeFromLaravel(Request $request)
    {
            dd($request->all());
        $data = $request->validate([
            'title'       => 'required|string',
            'video'       => 'required|file',
            'description' => 'nullable|string',
            'is_ad'       => 'sometimes|boolean',
        ]);

        // Save locally first
        $path = $request->file('video')->store('videos');

        $video = Video::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'url'         => $path,
            'video_type'  => 'information',
            'category'    => $request->boolean('is_ad') ? 'shorts' : 'video',
            'user_id'     => $request->user()->id ?? null,
            'is_ad'       => $request->boolean('is_ad'),
        ]);

        // Push to PlayTube
        $client = new Client(['base_uri' => config('services.playtube.url')]);
        $response = $client->post('/api.php', [
            'multipart' => [
                ['name' => 'video', 'contents' => fopen(Storage::path($path), 'r')],
                ['name' => 'title', 'contents' => $video->title],
                ['name' => 'description', 'contents' => $video->description ?? ''],
                ['name' => 'is_short', 'contents' => $request->boolean('is_ad') ? 'yes' : 'no'],
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.playtube.token'),
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);
        if (!empty($body['video_id'])) {
            $video->update(['playtube_id' => $body['video_id']]);
        }

        return response()->json($video, 201);
    }

    /**
     * Web form submission wrapper for uploading a video and syncing to PlayTube.
     * Redirects back to the dashboard with a flash message.
     */
    public function storeFromLaravelWeb(Request $request)
    {
        $request->validate([
            'title'       => 'required|string',
            'video'       => 'required|file',
            'description' => 'nullable|string',
            'is_ad'       => 'sometimes|boolean',
        ]);

        // Reuse the API logic but adapt response handling
        $response = $this->storeFromLaravel($request);

        if ($response->status() >= 200 && $response->status() < 300) {
            return redirect()->route('player.dashboard')->with('success', 'Video uploaded and synced successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload video.');
    }
}
