<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Sport;
use Illuminate\Database\Seeder;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positionData = [
            'Soccer' => [
                ['name' => 'Goalkeeper', 'value' => 'GK'],
                ['name' => 'Right Back', 'value' => 'RB'],
                ['name' => 'Left Back', 'value' => 'LB'],
                ['name' => 'Center Back', 'value' => 'CB'],
                ['name' => 'Defensive Midfielder', 'value' => 'CDM'],
                ['name' => 'Central Midfielder', 'value' => 'CM'],
                ['name' => 'Attacking Midfielder', 'value' => 'CAM'],
                ['name' => 'Left Winger', 'value' => 'LW'],
                ['name' => 'Right Winger', 'value' => 'RW'],
                ['name' => 'Striker', 'value' => 'ST'],
            ],
            'BASKETBALL' => [
                ['name' => 'Point Guard', 'value' => 'PG'],
                ['name' => 'Shooting Guard', 'value' => 'SG'],
                ['name' => 'Small Forward', 'value' => 'SF'],
                ['name' => 'Power Forward', 'value' => 'PF'],
                ['name' => 'Center', 'value' => 'C'],
            ],
            'FOOTBALL' => [
                ['name' => 'Quarterback', 'value' => 'QB'],
                ['name' => 'Running Back', 'value' => 'RB'],
                ['name' => 'Wide Receiver', 'value' => 'WR'],
                ['name' => 'Tight End', 'value' => 'TE'],
                ['name' => 'Offensive Lineman', 'value' => 'OL'],
                ['name' => 'Defensive Lineman', 'value' => 'DL'],
                ['name' => 'Linebacker', 'value' => 'LB'],
                ['name' => 'Cornerback', 'value' => 'CB'],
                ['name' => 'Safety', 'value' => 'S'],
                ['name' => 'Kicker', 'value' => 'K'],
            ],
            'BASEBALL' => [
                ['name' => 'Pitcher', 'value' => 'P'],
                ['name' => 'Catcher', 'value' => 'C'],
                ['name' => 'First Base', 'value' => '1B'],
                ['name' => 'Second Base', 'value' => '2B'],
                ['name' => 'Third Base', 'value' => '3B'],
                ['name' => 'Shortstop', 'value' => 'SS'],
                ['name' => 'Left Field', 'value' => 'LF'],
                ['name' => 'Center Field', 'value' => 'CF'],
                ['name' => 'Right Field', 'value' => 'RF'],
                ['name' => 'Designated Hitter', 'value' => 'DH'],
            ],
            'HOCKEY' => [
                ['name' => 'Goalie', 'value' => 'G'],
                ['name' => 'Left Defense', 'value' => 'LD'],
                ['name' => 'Right Defense', 'value' => 'RD'],
                ['name' => 'Left Wing', 'value' => 'LW'],
                ['name' => 'Center', 'value' => 'C'],
                ['name' => 'Right Wing', 'value' => 'RW'],
            ],
            'Field hockey' => [
                ['name' => 'Goalkeeper', 'value' => 'GK'],
                ['name' => 'Defender', 'value' => 'DF'],
                ['name' => 'Midfielder', 'value' => 'MF'],
                ['name' => 'Forward', 'value' => 'FW'],
            ],
            'Rugby' => [
                ['name' => 'Prop', 'value' => 'PR'],
                ['name' => 'Hooker', 'value' => 'HK'],
                ['name' => 'Lock', 'value' => 'LK'],
                ['name' => 'Flanker', 'value' => 'FL'],
                ['name' => 'Number Eight', 'value' => '8'],
                ['name' => 'Scrum-Half', 'value' => 'SH'],
                ['name' => 'Fly-Half', 'value' => 'FH'],
                ['name' => 'Centre', 'value' => 'CE'],
                ['name' => 'Wing', 'value' => 'WG'],
                ['name' => 'Fullback', 'value' => 'FB'],
            ],
            'Lacrosse' => [
                ['name' => 'Goalkeeper', 'value' => 'GK'],
                ['name' => 'Defender', 'value' => 'DF'],
                ['name' => 'Midfielder', 'value' => 'MF'],
                ['name' => 'Attacker', 'value' => 'AT'],
                ['name' => 'Faceoff Specialist', 'value' => 'FO'],
                ['name' => 'Long Stick Midfielder', 'value' => 'LSM'],
            ],
            'Volleyball' => [
                ['name' => 'Setter', 'value' => 'S'],
                ['name' => 'Outside Hitter', 'value' => 'OH'],
                ['name' => 'Opposite Hitter', 'value' => 'OPP'],
                ['name' => 'Middle Blocker', 'value' => 'MB'],
                ['name' => 'Libero', 'value' => 'L'],
                ['name' => 'Defensive Specialist', 'value' => 'DS'],
            ],
        ];

        foreach ($positionData as $sportName => $positions) {
            $sportId = Sport::where('name', $sportName)->value('id');

            if (! $sportId) {
                continue;
            }

            foreach ($positions as $position) {
                Position::updateOrCreate(
                    [
                        'sports_id' => $sportId,
                        'position_value' => $position['value'],
                    ],
                    [
                        'position_name' => $position['name'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
