<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('clubs')
            ->where(function ($query) {
                $query->whereNull('slug')->orWhere('slug', '');
            })
            ->select('id', 'name', 'slug')
            ->orderBy('id')
            ->chunkById(100, function ($clubs) {
                foreach ($clubs as $club) {
                    $base = Str::slug($club->name ?? 'club');
                    if ($base === '') {
                        $base = 'club';
                    }
                    $slug = $base;
                    $suffix = 1;
                    while (
                        DB::table('clubs')
                            ->where('id', '!=', $club->id)
                            ->where('slug', $slug)
                            ->exists()
                    ) {
                        $slug = $base . '-' . $suffix;
                        $suffix++;
                    }

                    DB::table('clubs')
                        ->where('id', $club->id)
                        ->update(['slug' => $slug]);
                }
            });
    }

    public function down(): void
    {
        // No rollback: slugs added for data integrity
    }
};
