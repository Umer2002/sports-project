<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogInteractionController extends Controller
{
    public function toggleLike(Request $request, Blog $blog)
    {
        $user = $request->user();

        $like = $blog->likes()->where('user_id', $user->id)->first();
        $liked = false;

        if ($like) {
            $like->delete();
        } else {
            $blog->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        $blog->loadCount('likes');

        return response()->json([
            'liked' => $liked,
            'likes_count' => $blog->likes_count,
        ]);
    }

    public function indexComments(Blog $blog)
    {
        $comments = $blog->comments()
            ->latest()
            ->take(40)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'name' => $comment->name ?: 'Teammate',
                    'comment' => $comment->comment,
                    'created_at' => optional($comment->created_at)->diffForHumans(),
                    'created_at_exact' => optional($comment->created_at)->format('M j, Y g:i A'),
                ];
            })
            ->values();

        $blog->loadCount('comments');

        return response()->json([
            'comments' => $comments,
            'comments_count' => $blog->comments_count,
        ]);
    }

    public function storeComment(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        $user = $request->user();

        $comment = $blog->comments()->create([
            'name' => $user->name,
            'email' => $user->email,
            'comment' => $data['comment'],
        ]);

        $blog->loadCount('comments');

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'name' => $comment->name ?: 'Teammate',
                'comment' => $comment->comment,
                'created_at' => optional($comment->created_at)->diffForHumans(),
                'created_at_exact' => optional($comment->created_at)->format('M j, Y g:i A'),
            ],
            'comments_count' => $blog->comments_count,
        ], 201);
    }
}
