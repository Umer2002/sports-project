<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sports')->insert([
            [
                'id' => 3,
                'name' => 'Soccer',
                'description' => 'Ashworth pays price for Man United recruitment as Ratcliffe shows ruthlessness',
                'icon_path' => 'logos/DqTVUdJbiToDBMV1bmBjaSb7lJCWGDlZem14dstl.png',
                'created_at' => '2025-03-24 16:38:00',
                'updated_at' => '2025-03-24 19:49:38',
                'is_top_sport' => 1,
            ],
            [
                'id' => 4,
                'name' => 'Basketball',
                'description' => 'Ashworth pays price for Man United recruitment as Ratcliffe shows ruthlessness',
                'icon_path' => 'logos/kJiCHK6bOSbK5deZXt9Lis6WX1t7GrxoGKRNl4h3.png',
                'created_at' => '2025-03-24 16:39:19',
                'updated_at' => '2025-03-24 19:54:55',
                'is_top_sport' => 1,
            ],
            [
                'id' => 5,
                'name' => 'Football',
                'description' => 'Ashworth pays price for Man United recruitment as Ratcliffe shows ruthlessness',
                'icon_path' => 'logos/oZfH5CMIbqjA7AkhywKCZxxaH1HeCBYiU5j3SKFT.png',
                'created_at' => '2025-03-24 16:40:20',
                'updated_at' => '2025-03-24 19:56:18',
                'is_top_sport' => 1,
            ],
            [
                'id' => 6,
                'name' => 'Baseball',
                'description' => 'Ashworth pays price for Man United recruitment as Ratcliffe shows ruthlessness',
                'icon_path' => 'logos/HZPrTD78LUGjEZ5q22I7wxYpC7U2v9vaHpRD4hm8.png',
                'created_at' => '2025-03-24 16:41:11',
                'updated_at' => '2025-03-24 20:07:14',
                'is_top_sport' => 1,
            ],
            [
                'id' => 7,
                'name' => 'Hockey',
                'description' => 'Ashworth pays price for Man United recruitment as Ratcliffe shows ruthlessness',
                'icon_path' => 'logos/YHWmPZ7u7YF1bbzxqY6tR3CR4402G1yOOQohIKvK.png',
                'created_at' => '2025-03-24 16:42:03',
                'updated_at' => '2025-03-24 19:56:30',
                'is_top_sport' => 1,
            ],
            [
                'id' => 8,
                'name' => 'Field Hockey',
                'description' => null,
                'icon_path' => 'logos/r3m1Jn2agk5anbguyyKflGDmgzB6G8qLzo7yMdMQ.webp',
                'created_at' => '2025-03-24 16:43:48',
                'updated_at' => '2025-03-24 19:56:39',
                'is_top_sport' => 0,
            ],
            [
                'id' => 9,
                'name' => 'Rugby',
                'description' => null,
                'icon_path' => 'logos/RNJKrzKBKWv7wrp9IhAuYTigTiPHhpPZIAJdx1wL.webp',
                'created_at' => '2025-03-24 16:44:19',
                'updated_at' => '2025-03-24 19:56:49',
                'is_top_sport' => 0,
            ],
            [
                'id' => 10,
                'name' => 'Boxing',
                'description' => null,
                'icon_path' => 'logos/hTR8Zl2wjjv8GN7uSuh8jqGRAYXBzHUPP7EkRJG1.webp',
                'created_at' => '2025-03-24 16:44:42',
                'updated_at' => '2025-03-24 19:56:59',
                'is_top_sport' => 0,
            ],
            [
                'id' => 11,
                'name' => 'Gymnastics',
                'description' => null,
                'icon_path' => 'logos/JpwCiMX47EMFBPtMVsuQ6kIdZSr4END872f6BfXS.webp',
                'created_at' => '2025-03-24 16:45:03',
                'updated_at' => '2025-03-24 19:57:10',
                'is_top_sport' => 0,
            ],
            [
                'id' => 12,
                'name' => 'Volleyball',
                'description' => null,
                'icon_path' => 'logos/w6f0OJcrNm6e2ILrilR0G2UroLB5MIi0yaFSrJU8.webp',
                'created_at' => '2025-03-24 16:45:28',
                'updated_at' => '2025-03-24 19:57:35',
                'is_top_sport' => 0,
            ],
            [
                'id' => 13,
                'name' => 'Swimming',
                'description' => null,
                'icon_path' => 'logos/IdNnKsqxRGM1eUnLOnrzQa7BzrnovW8YQqo2wSJ3.webp',
                'created_at' => '2025-03-24 16:45:47',
                'updated_at' => '2025-03-24 19:57:45',
                'is_top_sport' => 0,
            ],
            [
                'id' => 14,
                'name' => 'Lacrosse',
                'description' => null,
                'icon_path' => 'logos/Mxf3JNGMHFfnM3S4cPzcWJrgyRuENG3Akt3CkhYD.webp',
                'created_at' => '2025-03-24 16:46:06',
                'updated_at' => '2025-03-24 19:58:50',
                'is_top_sport' => 0,
            ],
            [
                'id' => 15,
                'name' => 'Wrestling',
                'description' => null,
                'icon_path' => 'logos/L3UM6srbbhty9Up3xNCjce4yyoy0rDesutMvDCwd.webp',
                'created_at' => '2025-03-24 16:46:25',
                'updated_at' => '2025-03-24 19:58:36',
                'is_top_sport' => 0,
            ],
            [
                'id' => 16,
                'name' => 'Mixed Martial Arts',
                'description' => null,
                'icon_path' => 'logos/IPxkMWmepsAmXgTIQdnPSevpAtVbjoVRxkgtXgxs.webp',
                'created_at' => '2025-03-24 16:46:57',
                'updated_at' => '2025-03-24 19:59:14',
                'is_top_sport' => 0,
            ],
            [
                'id' => 17,
                'name' => 'Track and Field',
                'description' => null,
                'icon_path' => 'logos/wJ8TZGBNKkVZwv9gVa42REXOkfuQ61VaepyF2OHq.webp',
                'created_at' => '2025-03-24 16:47:33',
                'updated_at' => '2025-03-24 19:59:26',
                'is_top_sport' => 0,
            ],
            [
                'id' => 18,
                'name' => 'Golf',
                'description' => null,
                'icon_path' => 'logos/TCfvCEgoXVbwVHEt5FLyLt9N1kkkuv6HIMRH4UGT.webp',
                'created_at' => '2025-03-24 16:47:53',
                'updated_at' => '2025-03-24 19:59:44',
                'is_top_sport' => 0,
            ],
            [
                'id' => 19,
                'name' => 'Tennis',
                'description' => null,
                'icon_path' => 'logos/OptpxoucrIA9f7rJWeHwPzazXtzUrRyqjGOzCuYw.webp',
                'created_at' => '2025-03-24 16:48:12',
                'updated_at' => '2025-03-24 19:59:55',
                'is_top_sport' => 0,
            ],
        ]);
    }
}
