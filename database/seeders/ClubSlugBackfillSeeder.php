<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClubSlugBackfillSeeder extends Seeder
{
    public function run(): void
    {
        Club::query()
            ->where(function ($query) {
                $query->whereNull('slug')->orWhere('slug', '');
            })
            ->orderBy('id')
            ->chunkById(100, function ($clubs) {
                foreach ($clubs as $club) {
                    $club->slug = $this->makeUniqueSlug($club);
                    $club->save();
                }
            });
    }

    private function makeUniqueSlug(Club $club): string
    {
        $base = Str::slug($club->name ?? 'club');
        if ($base === '') {
            $base = 'club';
        }

        $slug = $base;
        $suffix = 1;

        while (
            Club::query()
                ->where('id', '!=', $club->id)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}

