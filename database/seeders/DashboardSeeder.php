<?php

namespace Database\Seeders;

use App\Models\Club;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedClubsAndUsers();
        $this->seedPlayersAndUsers();
    }

    private function seedAdmin()
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('password'),
                'is_admin' => 1,

                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        // Ensure roles exist without violating unique constraints
        foreach ([
            ['name' => 'admin', 'label' => 'admin'],
            ['name' => 'club', 'label' => 'club'],
            ['name' => 'volunteer', 'label' => 'volunteer'],
            ['name' => 'player', 'label' => 'player'],
            ['name' => 'referee', 'label' => 'referee'],
        ] as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                ['label' => $role['label'], 'updated_at' => now(), 'created_at' => DB::raw('COALESCE(created_at, NOW())')]
            );
        }

        // Attach admin role to admin user if not already attached
        $adminUserId = DB::table('users')->where('email', 'admin@admin.com')->value('id');
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        if ($adminUserId && $adminRoleId) {
            $exists = DB::table('role_user')
                ->where('user_id', $adminUserId)
                ->where('role_id', $adminRoleId)
                ->exists();
            if (!$exists) {
                DB::table('role_user')->insert([
                    'user_id' => $adminUserId,
                    'role_id' => $adminRoleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        DB::table('venues')->insert(
            [
                'name' => 'Venue 1',
                'location' => 'LA',
                'capacity' => '5000',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function seedClubsAndUsers()
    {
        $clubs = [
            [
                'name' => 'FC Barcelona',
                'email' => 'barcelona@barcelona.com',
                'user_email' => 'club1@admin.com',
                'sport' => 'Soccer',
                'social_links' => [
                    'facebook' => 'https://www.facebook.com/FCBarcelona',
                    'instagram' => 'https://www.instagram.com/fcbarcelona/',
                    'twitter' => 'https://twitter.com/FCBarcelona',
                    'website' => 'https://www.fcbarcelona.com/',
                    'youtube' => 'https://www.youtube.com/user/FCBarcelona',
                ],
            ],
            [
                'name' => 'Real Madrid',
                'email' => 'realmadrid@realmadrid.com',
                'user_email' => 'club2@admin.com',
                'sport' => 'Soccer',
                'social_links' => [
                    'instagram' => 'https://www.instagram.com/realmadrid/',
                    'website' => 'https://www.realmadrid.com/',
                    'youtube' => 'https://www.youtube.com/user/RealMadridCF',
                ],
            ],
            [
                'name' => 'FC Bayern München',
                'email' => 'fcbayern@fcbayern.com',
                'user_email' => 'club3@admin.com',
                'sport' => 'Soccer',
                'social_links' => [
                    'instagram' => 'https://www.instagram.com/fcbayernmunich/',
                    'website' => 'https://fcbayern.com/',
                    'youtube' => 'https://www.youtube.com/user/FCBAYERNMUNICH',
                ],
            ],
        ];

        foreach ($clubs as $index => $club) {
            $sportId = $this->resolveSportId($club['sport'] ?? 'Soccer');

            DB::table('users')->updateOrInsert(
                ['email' => $club['user_email']],
                [
                    'name' => 'club' . ($index + 1),
                    'password' => Hash::make('password'),
                    'is_admin' => 0,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $userId = DB::table('users')->where('email', $club['user_email'])->value('id');
            Club::updateOrCreate(
                ['email' => $club['email']],
                [
                    'name' => $club['name'],
                    'user_id' => $userId,
                    'bio' => "{$club['name']} is a professional football club...",
                    'logo' => 'https://via.placeholder.com/120',
                    'address' => 'Some Address',
                    'phone' => '+123456789',
                    'joining_url' => 'https://transfermarkt.com',
                    'paypal_link' => 'https://paypal.me/fake',
                    'is_registered' => 1,
                    'sport_id' => $sportId,
                    'social_links' => $club['social_links'],
                ]
            );
        }
    }

    private function seedPlayersAndUsers()
    {
        $players = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'johndoe@example.com', 'gender' => 'Male', 'club_id' => 1, 'user_email' => 'player1@p.com'],
            ['first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'janedoe@example.com', 'gender' => 'Female', 'club_id' => 1, 'user_email' => 'player2@p.com'],
            ['first_name' => 'John', 'last_name' => 'Smith', 'email' => 'johns@example.com', 'gender' => 'Male', 'club_id' => 2, 'user_email' => 'player3@p.com'],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'janesmith@example.com', 'gender' => 'Female', 'club_id' => 2, 'user_email' => 'player4@p.com'],
        ];

        foreach ($players as $index => $player) {
            DB::table('users')->updateOrInsert(
                ['email' => $player['user_email']],
                [
                    'name' => 'player' . ($index + 1),
                    'password' => Hash::make('password'),
                    'is_admin' => 0,

                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $userId = DB::table('users')->where('email', $player['user_email'])->value('id');
            DB::table('players')->updateOrInsert(
                ['email' => $player['email']],
                [
                    'name' => $player['first_name'] . ' ' . $player['last_name'],
                    'phone' => '+1 123 456 7890',
                    'gender' => $player['gender'],
                    'club_id' => $player['club_id'],
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $playerRecord = DB::table('players')->where('email', $player['email'])->first();
        }
    }

    private function resolveSportId(string $sportName): int
    {
        $normalized = strtolower(trim($sportName));

        $existing = DB::table('sports')
            ->select('id')
            ->whereRaw('LOWER(name) = ?', [$normalized])
            ->first();

        if ($existing) {
            return (int) $existing->id;
        }

        return (int) DB::table('sports')->insertGetId([
            'name' => strtoupper($sportName),
            'description' => null,
            'icon_path' => null,
            'is_top_sport' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
