<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FieldHockeyClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Field Hockey sport_id (your table shows "Field hockey")
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['field hockey','field-hockey','hockey (field)'])
            ->orWhereIn('name', ['Field hockey','FIELD HOCKEY'])
            ->value('id');

        if (!$sportId) return; // sport not found

        // 2) Define groups (code => [name, sort_order])
        $groups = [
            'FH_STARTER'        => ['LEARN-TO-PLAY & STARTER PROGRAMS', 10],
            'FH_YOUTH_COMM'     => ['YOUTH CLUB / COMMUNITY LEAGUES', 20],
            'FH_SCHOOL'         => ['SCHOOL / SCHOLASTIC', 30],
            'FH_COLLEGE'        => ['COLLEGE / UNIVERSITY', 40],
            'FH_ADULT'          => ['ADULT & SENIOR LEAGUES', 50],
            'FH_ELITE_NAT'      => ['ELITE & NATIONAL PROGRAMS', 60],
            'FH_TOURNEY'        => ['TOURNAMENT & CLUB TEAM FLIGHTS', 70],
            'FH_SPECIAL'        => ['SPECIAL FORMATS & VARIANTS', 80],
            'FH_SEASONAL'       => ['SEASONAL & INTRAMURAL', 90],
            'FH_SKILL_BAND'     => ['SKILL / COMPETITIVE BANDS', 100],
        ];

        // Upsert groups & get IDs
        $groupIds = [];
        foreach ($groups as $code => [$name, $order]) {
            DB::table('sport_classification_groups')->updateOrInsert(
                ['sport_id' => $sportId, 'code' => $code],
                ['name' => $name, 'sort_order' => $order, 'created_at' => $now, 'updated_at' => $now]
            );
            $groupIds[$code] = DB::table('sport_classification_groups')
                ->where(['sport_id' => $sportId, 'code' => $code])
                ->value('id');
        }

        // Helper
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

        $append = function (string $groupCode, array $row, ?array $metaOverride = null) use (&$payload, $opt) {
            $values = array_values($row);
            $code   = $values[0] ?? null;
            $label  = $values[1] ?? null;
            $order  = $values[2] ?? 0;
            $rank   = $values[3] ?? null;
            $meta   = $values[4] ?? null;

            if ($meta === null && is_array($rank)) {
                $meta = $rank;
                $rank = null;
            }

            if ($metaOverride !== null) {
                $meta = $metaOverride;
            }

            $payload[] = $opt($groupCode, $code, $label, $order, $rank, $meta);
        };

        // 3) OPTIONS

        // STARTER
        $starter = [
            ['ST_MINI_STIX','Mini Sticks / FunStix',10, null, ['intro'=>true]],
            ['ST_ACTIVE_START','Active Start',20, null, ['intro'=>true]],
            ['ST_LEARN_PLAY','Learn to Play / Fundamentals',30, null, ['intro'=>true]],
        ];
        foreach ($starter as $row) {
            $append('FH_STARTER', $row);
        }

        // YOUTH CLUB / COMMUNITY
        $youth = [
            ['YTH_HOUSE','House / Recreational',10,90],
            ['YTH_DEV_GRASS','Development / Grassroots',20,80],
            ['YTH_SELECT_TRAVEL','Select / Travel',30,70],
            ['YTH_JUNIOR_LG','Junior League',40,65],
            ['YTH_REG_DEV_SQUADS','Regional Development Squads',50,50],
            ['YTH_PROV_STATE','Provincial / State Teams',60,40],
            ['YTH_HPA','High-Performance Academy',70,30],
            ['YTH_NAT_JR','National Junior Teams (U16 / U18 / U21)',80,20],
        ];
        foreach ($youth as $row) {
            $append('FH_YOUTH_COMM', $row);
        }

        // SCHOOL / SCHOLASTIC
        $school = [
            ['SCH_ELEM','Elementary / Primary School',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HS_FRESH','High School: Freshman',30],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',40],
            ['SCH_HS_VARS','High School: Varsity',50],
            ['SCH_PREP_IND','Prep / Independent School',60],
        ];
        foreach ($school as $row) {
            $append('FH_SCHOOL', $row);
        }

        // COLLEGE / UNIVERSITY
        $collegeUS = [
            ['US_NCAA_D3','NCAA Division III',10,50],
            ['US_NCAA_D2','NCAA Division II',20,40],
            ['US_NCAA_D1','NCAA Division I',30,20],
            ['US_CLUB_INTRAM','Club / Intramural Leagues (A/B/C)',40,80],
        ];
        foreach ($collegeUS as $row) {
            $append('FH_COLLEGE', $row);
        }

        $collegeCA = [
            ['CA_U_SPORTS_W','U Sports Women’s Field Hockey',50,25, ['country'=>'CA','female'=>true]],
            ['CA_OUA_CW','OUA / Canada West Leagues',60,30, ['country'=>'CA']],
            ['CA_COLLEGE_CLUB','College Club Leagues',70,70, ['country'=>'CA']],
        ];
        foreach ($collegeCA as $row) {
            $append('FH_COLLEGE', $row);
        }

        // ADULT & SENIOR
        $adult = [
            ['AD_OPEN_PREM','Open / Premier',10,10],
            ['AD_COMP_A','Competitive A',20,20],
            ['AD_COMP_B','Competitive B',30,30],
            ['AD_COMP_C','Competitive C',40,40],
            ['AD_REC_SOCIAL','Recreational / Social',50,60],
            ['AD_MASTERS_O30','Masters O30',60,70],
            ['AD_MASTERS_O35','Masters O35',70,75],
            ['AD_MASTERS_O40','Masters O40',80,80],
            ['AD_MASTERS_O45','Masters O45',90,85],
            ['AD_MASTERS_O50','Masters O50+',100,90],
        ];
        foreach ($adult as $row) {
            $append('FH_ADULT', $row);
        }

        // ELITE & NATIONAL PROGRAMS
        $elite = [
            ['EL_REG_HPC','Regional High-Performance Centers',10,50],
            ['EL_PROV_HP','Provincial High-Performance Teams',20,40],
            ['EL_JUN_NAT','Junior National Teams (Men’s & Women’s)',30,20],
            ['EL_SEN_NAT','Senior National Teams (Men’s & Women’s)',40,10],
            ['EL_INDOOR_NAT','Indoor National Teams (for indoor format)',50,30, ['indoor'=>true]],
        ];
        foreach ($elite as $row) {
            $append('FH_ELITE_NAT', $row);
        }

        // TOURNAMENT & CLUB TEAM FLIGHTS
        $flights = [
            ['FLT_ELITE','Elite',10,10],
            ['FLT_PLAT','Platinum',20,20],
            ['FLT_GOLD','Gold',30,30],
            ['FLT_SILVER','Silver',40,40],
            ['FLT_BRONZE','Bronze',50,50],
            ['FLT_OPEN_CHAMP','Open / Championship',60,60],
            ['FLT_FLIGHT_1','Flight 1',70,70],
            ['FLT_FLIGHT_2','Flight 2',80,80],
            ['FLT_FLIGHT_3','Flight 3',90,90],
        ];
        foreach ($flights as $row) {
            $append('FH_TOURNEY', $row);
        }

        // SPECIAL FORMATS & VARIANTS
        $special = [
            ['SP_INDOOR_5V5','Indoor Field Hockey (5-a-side)',10, null, ['team_size'=>5,'format'=>'indoor']],
            ['SP_OUTDOOR_7V7','7-a-side Outdoor',20, null, ['team_size'=>7,'format'=>'outdoor']],
            ['SP_SMALL_5V5','5-a-side Small-Sided',30, null, ['team_size'=>5,'format'=>'small-sided']],
            ['SP_BEACH','Beach Hockey',40, null, ['surface'=>'beach']],
            ['SP_MASTERS_WC','Masters World Cup Divisions',50, null, ['masters'=>true]],
            ['SP_PARAHOCKEY','Parahockey / Inclusive or Unified Programs',60, null, ['inclusive'=>true]],
        ];
        foreach ($special as $row) {
            $append('FH_SPECIAL', $row);
        }

        // SEASONAL & INTRAMURAL
        $seasonal = [
            ['SEAS_FALL','Fall League',10],
            ['SEAS_WINTER_INDOOR','Winter Indoor League',20, null, ['indoor'=>true]],
            ['SEAS_SPRING_DEV','Spring Development League',30],
            ['SEAS_SUMMER','Summer League',40],
            ['SEAS_COL_INTRAM','College Intramural A / B / C',50],
        ];
        foreach ($seasonal as $row) {
            $append('FH_SEASONAL', $row);
        }

        // SKILL / COMPETITIVE BANDS
        $bands = [
            ['AAA','AAA',10,10],
            ['AA','AA',20,20],
            ['A','A',30,30],
            ['B','B',40,40],
            ['C','C',50,50],
        ];
        foreach ($bands as [$code,$label,$order,$rank]) {
            $payload[] = $opt('FH_SKILL_BAND','SKILL_'.$code,$label,$order,$rank,['band'=>$label]);
        }

        // 4) Upsert
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
