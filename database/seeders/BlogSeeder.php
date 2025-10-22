<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Blog;
use App\Models\BlogCategory;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $category = BlogCategory::firstOrCreate(['title' => 'Admin']);

        $posts = [
            [
                'title'   => 'Welcome to the Admin Blog',
                'content' => '<p>This is a sample post to kick things off.</p>',
            ],
            [
                'title'   => 'Platform Updates & Release Notes',
                'content' => '<p>We will publish periodic updates and changelogs here.</p>',
            ],
            [
                'title'   => 'How to Use the Dashboard Efficiently',
                'content' => '<p>Quick tips to speed up your workflow in the admin panel.</p>',
            ],
            [
                'title'   => 'Content Guidelines for Team Members',
                'content' => '<p>Please follow these guidelines when publishing content.</p>',
            ],
            [
                'title'   => 'Security Best Practices',
                'content' => '<p>Remember to rotate API keys and enable 2FA.</p>',
            ],
        ];

        foreach ($posts as $i => $p) {
            Blog::updateOrCreate(
                ['slug' => Str::slug($p['title'])],
                [
                    'blog_category_id' => $category->id,
                    'user_id'          => 1,      // adjust if needed
                    'club_id'          => null,   // or set a valid club id
                    'title'            => $p['title'],
                    'content'          => $p['content'],
                    'image'            => 'public/images/card-inner-place-holder.png',
                    'views'            => 0,
                ]
            );
        }
    }
}
