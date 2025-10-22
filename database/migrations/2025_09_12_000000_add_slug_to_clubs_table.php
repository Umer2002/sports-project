<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            if (!Schema::hasColumn('clubs', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
        });

        $clubs = DB::table('clubs')->select('id', 'name', 'slug')->get();
        foreach ($clubs as $club) {
            if ($club->slug) {
                continue;
            }

            $baseSlug = Str::slug($club->name);
            if ($baseSlug === '') {
                $baseSlug = 'club-' . $club->id;
            }

            $slug = $baseSlug;
            $suffix = 1;

            while (DB::table('clubs')->where('slug', $slug)->where('id', '!=', $club->id)->exists()) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }

            DB::table('clubs')->where('id', $club->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clubs', 'slug')) {
            Schema::table('clubs', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
