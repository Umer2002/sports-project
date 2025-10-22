<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin',       // platform admin
            'player',
            'club',
            'referee',
            'college',     // college / university
            'coach',
            'volunteer',   // used in code for ambassadors in some places
            'ambassador',  // seed alias to avoid mismatches
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
    }
}

