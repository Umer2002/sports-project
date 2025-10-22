<?php
namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Exception;

class YouTubeController extends Controller
{
    public function uploadPendingVideos()
    {
        // Fetch all videos with 'pending' status
        $videos = Video::where('status', 'pending')->get();
        if ($videos->isEmpty()) {
            return response()->json(['message' => 'No pending videos found.']);
        }

        foreach ($videos as $video) {
            try {
                // Full path to the local file
                $filePath = storage_path("app/videos/temp/{$video->title}.mp4");

                if (!Storage::exists("videos/temp/{$video->title}.mp4")) {
                    continue; // Skip if file doesn't exist
                }

                // Upload video to YouTube
                $youtubeId = $this->uploadToYouTube($filePath, $video->title, $video->sport, $video->category);

                if ($youtubeId) {
                    // Update video record in database
                    $video->update([
                        'youtube_id' => $youtubeId,
                        'status' => 'uploaded'
                    ]);

                    // Delete the local file
                    Storage::delete("videos/temp/{$video->title}.mp4");
                }
            } catch (Exception $e) {
                return response()->json(['error' => 'YouTube upload failed', 'message' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Pending videos uploaded successfully.']);
    }

    private function uploadToYouTube($filePath, $title, $sport, $category)
    {
        // Initialize YouTube API Client
        $client = new Google_Client();
        $client->setApplicationName("Laravel YouTube Uploader");
        $client->setDeveloperKey(env('YOUTUBE_API_KEY'));

        $youtube = new Google_Service_YouTube($client);

        // Set up video snippet (metadata)
        $snippet = new Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($title);
        $snippet->setDescription("Uploaded via Laravel - Sport: $sport, Category: $category");
        $snippet->setTags(["sports", $sport, $category]);
        $snippet->setCategoryId("17"); // "Sports" category in YouTube

        // Set video status (public, unlisted, or private)
        $status = new Google_Service_YouTube_VideoStatus();
        $status->setPrivacyStatus("public");

        // Create video object
        $video = new Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        // Upload video file
        $chunkSizeBytes = 1 * 1024 * 1024; // 1MB chunks
        $client->setDefer(true);

        $insertRequest = $youtube->videos->insert("status,snippet", $video);

        $media = new \Google_Http_MediaFileUpload(
            $client,
            $insertRequest,
            'video/*',
            null,
            true,
            $chunkSizeBytes
        );
        $media->setFileSize(filesize($filePath));

        $status = false;
        $handle = fopen($filePath, "rb");

        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);

        if ($status->status['uploadStatus'] == 'uploaded') {
            return $status['id']; // Return YouTube video ID
        }

        return null;
    }
}
