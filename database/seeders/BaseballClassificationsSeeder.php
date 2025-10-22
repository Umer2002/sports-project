<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseballClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Find Baseball sport_id (be forgiving on name/case)
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['baseball'])
            ->orWhere('name', 'BASEBALL')
            ->value('id');

        if (!$sportId) {
            // No Baseball sport row found; skip quietly
            return;
        }

        // 2) Define groups (code => [name, sort_order])
        $groups = [
            'DEV_COMM_BB'     => ['YOUTH DEVELOPMENT / COMMUNITY', 10],
            'COMP_TIER_BB'    => ['COMPETITIVE YOUTH TIERS', 20],
            'SCHOOL_BB'       => ['SCHOOL BASEBALL', 30],
            'COLLEGE_BB'      => ['COLLEGE / UNIVERSITY', 40],
            'ADULT_AM_BB'     => ['ADULT AMATEUR & SENIOR LEAGUES', 50],
            'PRO_BB'          => ['PROFESSIONAL / SEMI-PRO', 60],
            'SPECIAL_BB'      => ['SPECIALTY FORMATS', 70],
            'TOURNEY_FLT_BB'  => ['TOURNAMENT FLIGHTS / LABELS', 80],
            'SEASONAL_INTR_BB'=> ['SEASONAL & INTRAMURAL', 90],
            'SKILL_BAND_BB'   => ['SKILL / COMPETITIVE BANDS', 100],
        ];

        // Upsert groups & collect IDs
        $groupIds = [];
        foreach ($groups as $code => [$name, $order]) {
            DB::table('sport_classification_groups')->updateOrInsert(
                ['sport_id' => $sportId, 'code' => $code],
                ['name' => $name, 'sort_order' => $order, 'updated_at' => $now, 'created_at' => $now]
            );
            $groupIds[$code] = DB::table('sport_classification_groups')
                ->where(['sport_id' => $sportId, 'code' => $code])
                ->value('id');
        }

        // Helper to build option rows
        $opt = function($groupCode, $code, $label, $order, $numericRank=null, $meta=null) use ($groupIds, $now) {
            return [
                'group_id'     => $groupIds[$groupCode],
                'code'         => $code,
                'label'        => $label,
                'sort_order'   => $order,
                'numeric_rank' => $numericRank,
                'meta'         => $meta ? json_encode($meta) : null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        };

        $payload = [];

        $append = function (string $groupCode, array $row) use (&$payload, $opt) {
            $code  = $row[0];
            $label = $row[1];
            $order = $row[2];
            $rank  = $row[3] ?? null;
            $meta  = $row[4] ?? null;

            $payload[] = $opt($groupCode, $code, $label, $order, $rank, $meta);
        };

        // 3) OPTIONS

        // YOUTH DEVELOPMENT / COMMUNITY
        $youth = [
            ['YTH_LEARN_PLAY','Learn-to-Play / Rally Cap / Rookie Ball',10],
            ['YTH_TBALL','T-Ball',20, null, ['variant'=>'tball']],
            ['YTH_COACH_MACHINE','Coach Pitch / Machine Pitch',30],
            ['YTH_MINORS_A_AA','Minors A / AA',40],
            ['YTH_MINORS_AAA','Minors AAA',50],
            ['YTH_MAJORS_LL','Majors (Little League Majors)',60],
            ['YTH_INTERMEDIATE_50_70','Intermediate (50/70)',70, null, ['diamond'=>'50/70']],
            ['YTH_JUNIORS','Juniors',80],
            ['YTH_SENIORS','Seniors',90],

            // Pony League levels
            ['PONY_SHETLAND','Pony League: Shetland',100],
            ['PONY_PINTO','Pony League: Pinto',110],
            ['PONY_MUSTANG','Pony League: Mustang',120],
            ['PONY_BRONCO','Pony League: Bronco',130],
            ['PONY_PONY','Pony League: Pony',140],
            ['PONY_COLT','Pony League: Colt',150],
            ['PONY_PALOMINO','Pony League: Palomino',160],

            // Cal Ripken divisions
            ['CR_TBALL','Cal Ripken: T-Ball',170],
            ['CR_ROOKIE','Cal Ripken: Rookie',180],
            ['CR_MINOR','Cal Ripken: Minor',190],
            ['CR_MAJOR','Cal Ripken: Major',200],
            ['CR_MAJOR_70','Cal Ripken: Major 70',210, null, ['diamond'=>'70']],

            // Babe Ruth age bands
            ['BR_13_15','Babe Ruth League: 13-15',220, null, ['age_band'=>'13-15']],
            ['BR_16_18','Babe Ruth League: 16-18',230, null, ['age_band'=>'16-18']],
        ];
        foreach ($youth as $row) {
            $append('DEV_COMM_BB', $row);
        }

        // COMPETITIVE YOUTH TIERS
        $compYouth = [
            ['REC_HOUSE','Recreational / House League',10,90],
            ['SELECT','Select',20,70],
            ['ALL_STAR_TRAVEL','All-Star / Travel',30,60],
            ['REP','Rep / Representative',40,50],
            ['SILVER','Silver',50,40],
            ['GOLD','Gold',60,30],
            ['ELITE_HP','Elite / High-Performance',70,20],
            ['PROV_STATE_TEAM','Provincial / State Team',80,15],
            ['REGIONAL_DISTRICT','Regional / District Team',90,25],
            ['NAT_DEV_TEAM','National Development Team',100,10],
        ];
        foreach ($compYouth as $row) {
            $append('COMP_TIER_BB', $row);
        }

        // SCHOOL BASEBALL
        $school = [
            ['SCH_ELEM','Elementary / Primary',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HS_FRESHMAN','High School: Freshman',30],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',40],
            ['SCH_HS_VARSITY','High School: Varsity',50],
            ['SCH_PREP_ACAD','Prep / Academy Programs',60],
        ];
        foreach ($school as $row) {
            $append('SCHOOL_BB', $row);
        }

        // COLLEGE / UNIVERSITY
        $collegeUSA = [
            ['USA_NJCAA','USA: NJCAA (Junior College)',10,60],
            ['USA_NAIA','USA: NAIA',20,55],
            ['USA_NCAA_D3','USA: NCAA Division III',30,50],
            ['USA_NCAA_D2','USA: NCAA Division II',40,40],
            ['USA_NCAA_D1','USA: NCAA Division I',50,20],
        ];
        foreach ($collegeUSA as $row) {
            $append('COLLEGE_BB', $row);
        }

        $collegeCAN = [
            ['CAN_CCBC','Canada: CCBC (Canadian College Baseball Conference)',60,35],
            ['CAN_U_SPORTS_CLUBS','Canada: U Sports Baseball Clubs',70,45],
        ];
        foreach ($collegeCAN as $row) {
            $append('COLLEGE_BB', $row);
        }

        // ADULT AMATEUR & SENIOR LEAGUES
        $adult = [
            ['AD_HOUSE_COMM','Adult House / Community',10],
            ['AD_MENS_OPEN','Men’s Open',20],
            ['AD_SENIOR_MENS','Senior Men’s (O30, O35, O40, O45, O50)',30, null, ['masters_bands'=>['O30','O35','O40','O45','O50']]],
            ['AD_CORP_BEER','Industrial / Corporate / Beer Leagues',40],
            ['AD_MASTERS_VINTAGE','Masters / Vintage Baseball',50],
            ['AD_WOOD_BAT','Wood-Bat Leagues',60, null, ['equipment'=>'wood_bat']],
            ['AD_FASTPITCH_XOVER','Men’s / Women’s Fastpitch Softball crossover leagues',70, null, ['crossover'=>'fastpitch']],
        ];
        foreach ($adult as $row) {
            $append('ADULT_AM_BB', $row);
        }

        // PROFESSIONAL / SEMI-PRO (use numeric_rank for hierarchy: lower = higher)
        $pro = [
            ['PRO_MLB','Major League Baseball (MLB)',10,1],
            ['PRO_TRIPLE_A','Triple-A (AAA)',20,2],
            ['PRO_DOUBLE_A','Double-A (AA)',30,3],
            ['PRO_HIGH_A','High-A (A+)',40,4],
            ['PRO_SINGLE_A','Single-A (A) / Low-A',50,5],
            ['PRO_ROOKIE','Rookie / Complex Leagues',60,6],
            ['PRO_INDEPENDENT','Independent Pro Leagues (Atlantic, Frontier, American Association, etc.)',70,8],
            ['PRO_MEXICAN','Mexican League',80,7],
            ['PRO_WINTER_BALL','Winter Ball (Dominican, Puerto Rican, Venezuelan, etc. with NA players)',90,9],
        ];
        foreach ($pro as $row) {
            $append('PRO_BB', $row);
        }

        // SPECIALTY FORMATS
        $special = [
            ['SP_SOFTBALL_FAST','Softball (Fastpitch)',10, null, ['softball'=>'fastpitch']],
            ['SP_SOFTBALL_SLOW','Softball (Slowpitch)',20, null, ['softball'=>'slowpitch']],
            ['SP_BASEBALL5','Baseball5 (WBSC 5-on-5)',30, null, ['team_size'=>5,'variant'=>'baseball5']],
            ['SP_INDOOR_DOME','Indoor / Dome Ball',40, null, ['surface'=>'indoor']],
            ['SP_WIFFLE','Wiffle / Plastic Bat',50, null, ['variant'=>'wiffle']],
            ['SP_BLITZBALL','Blitzball',60],
            ['SP_STICKBALL','Stickball / Streetball',70],
            ['SP_SANDLOT','Sandlot / Pickup',80],
            ['SP_WHEELCHAIR','Wheelchair / Adaptive Baseball',90, null, ['inclusive'=>true]],
            ['SP_CHALLENGER','Challenger / Miracle League (inclusive/adapted)',100, null, ['inclusive'=>true]],
        ];
        foreach ($special as $row) {
            $append('SPECIAL_BB', $row);
        }

        // TOURNAMENT FLIGHTS / LABELS
        $flights = [
            ['FLT_ELITE','Elite',10,10],
            ['FLT_PLATINUM','Platinum',20,20],
            ['FLT_GOLD','Gold',30,30],
            ['FLT_SILVER','Silver',40,40],
            ['FLT_BRONZE','Bronze',50,50],
            ['FLT_OPEN','Open',60,60],
            ['FLT_CHALLENGER','Challenger',70,70],
            ['FLT_CHAMPIONSHIP','Championship',80,80],
            ['FLT_FLIGHT_1','Flight 1',90,90],
            ['FLT_FLIGHT_2','Flight 2',100,100],
            ['FLT_FLIGHT_3','Flight 3',110,110],
        ];
        foreach ($flights as $row) {
            $append('TOURNEY_FLT_BB', $row);
        }

        // SEASONAL & INTRAMURAL
        $seasonal = [
            ['SEAS_SPRING','Spring League',10],
            ['SEAS_SUMMER','Summer League',20],
            ['SEAS_FALL_BALL','Fall Ball',30],
            ['SEAS_WINTER_TRAIN','Winter Training League',40],
            ['SEAS_INTRAMURAL','College Intramural A / B / C',50],
        ];
        foreach ($seasonal as $row) {
            $append('SEASONAL_INTR_BB', $row);
        }

        // SKILL / COMPETITIVE BANDS
        $bands = ['AAA','AA','A','B','C','D'];
        $o=10; foreach ($bands as $label) {
            $payload[] = $opt('SKILL_BAND_BB','SKILL_'.$label,$label,$o, null, ['band'=>$label]);
            $o += 10;
        }

        // 4) Upsert all options
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
