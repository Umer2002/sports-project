<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BasketballClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Find Basketball sport_id (be forgiving on name)
        $basketballId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['basketball'])
            ->orWhere('name', 'BASKETBALL')
            ->value('id');

        if (!$basketballId) {
            // No Basketball found, skip seeding silently
            return;
        }

        // 2) Define groups (code => [name, order])
        $groups = [
            'DEV_STAGE_BBALL'   => ['YOUTH DEVELOPMENT STAGES (Canada Basketball LTAD style)', 10],
            'REC_LEVEL'         => ['RECREATIONAL / COMMUNITY LEVELS', 20],
            'COMP_TIER_BBALL'   => ['COMPETITIVE YOUTH / CLUB TIERS', 30],
            'SCHOOL_BBALL'      => ['SCHOOL BASKETBALL', 40],
            'POSTSEC_PRODEV'    => ['POST-SECONDARY & PRO DEVELOPMENT', 50],
            'PRO_PYRAMID_BBALL' => ['PROFESSIONAL / SEMI-PRO PYRAMID', 60],
            'ADULT_COMM'        => ['ADULT COMMUNITY & CITY LEAGUES', 70],
            'MASTERS'           => ['MASTERS / O-AGE BRACKETS', 80],
            'TOURNEY_AAU'       => ['TOURNAMENT & AAU FLIGHTS / BRACKETS', 90],
            'SPECIAL_VAR'       => ['SPECIAL FORMATS & VARIATIONS', 100],
            'SEASONAL_INTRAM'   => ['SEASONAL / INTRAMURAL', 110],
            'SKILL_BAND'        => ['SKILL-BAND LABELS (used in many leagues)', 120],
        ];

        // Upsert groups & collect IDs
        $groupIds = [];
        foreach ($groups as $code => [$name, $order]) {
            DB::table('sport_classification_groups')->updateOrInsert(
                ['sport_id' => $basketballId, 'code' => $code],
                ['name' => $name, 'sort_order' => $order, 'updated_at' => $now, 'created_at' => $now]
            );
            $groupIds[$code] = DB::table('sport_classification_groups')
                ->where(['sport_id' => $basketballId, 'code' => $code])
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

        // DEV STAGES (LTAD)
        $devStages = [
            'Active Start','FUNdamentals','Learn to Train','Train to Train',
            'Train to Compete','Train to Win','Basketball for Life'
        ];
        $o=10; foreach ($devStages as $label) {
            $payload[] = $opt('DEV_STAGE_BBALL','DEV_'.strtoupper(str_replace(' ','_',$label)),$label,$o); $o+=10;
        }

        // REC LEVELS
        $rec = [
            'Intro / Grassroots',
            'Mini-Ball / Little Hoopers',
            'House League',
            'Community League',
            'Recreational C',
            'Recreational B',
            'Recreational A',
            'Developmental / Rec+',
            'All-Star Rec Select',
        ];
        $o=10; foreach ($rec as $label) {
            $payload[] = $opt('REC_LEVEL','REC_'.strtoupper(preg_replace('/[^A-Z0-9]+/i','_',$label)),$label,$o); $o+=10;
        }

        // COMPETITIVE YOUTH / CLUB TIERS
        // Use numeric_rank to sort logically (lower rank = higher level)
        $tiers = [
            ['CLUB_DEV','Club Development',10,80],
            ['SELECT_TRAVEL','Select / Travel Team',20,70],
            ['AA','AA',30,60],
            ['AAA','AAA',40,50],
            ['DIV_4','Division 4',50,40],
            ['DIV_3','Division 3',60,30],
            ['DIV_2','Division 2',70,20],
            ['DIV_1','Division 1',80,10],
            ['SILVER','Silver',90,40],
            ['GOLD','Gold',100,30],
            ['PLATINUM','Platinum',110,20],
            ['ELITE_HP','Elite / High-Performance',120,15],
            ['PROV_TEAM','Provincial Team',130,12],
            ['REGIONAL_HPA','Regional High-Performance Academy',140,14],
            ['NTC_JNR','National Training Centre / Jr. National',150,8],
        ];
        foreach ($tiers as $row) {
            $append('COMP_TIER_BBALL', $row);
        }

        // SCHOOL BASKETBALL
        $school = [
            ['SCH_ELEMENTARY','Elementary / Primary School',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HS_FRESHMAN','High School: Freshman',30],
            ['SCH_HS_SOPHOMORE','High School: Sophomore',40],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',50],
            ['SCH_HS_VARSITY','High School: Varsity',60],
            ['SCH_PREP','Prep School / Prep Academy',70],
            ['SCH_COL_CLUB','College / University: Club',80],
            ['SCH_COL_VARSITY','College / University: Varsity (US: NCAA Div III, Div II, Div I; Canada: U Sports)',90],
        ];
        foreach ($school as $row) {
            $append('SCHOOL_BBALL', $row);
        }

        // POST-SECONDARY & PRO DEVELOPMENT
        $postPro = [
            ['JUCO','JUCO (Junior College)',10],
            ['CCAA','CCAA (Canada Colleges)',20],
            ['NAIA','NAIA',30],
            ['NCAA_D3','NCAA Div III',40],
            ['NCAA_D2','NCAA Div II',50],
            ['NCAA_D1','NCAA Div I',60],
            ['NBA_G','NBA G-League / Farm System',70],
            ['OVERSEAS_ACAD','Overseas Academy / International Pro-Prep',80],
        ];
        foreach ($postPro as $row) {
            $append('POSTSEC_PRODEV', $row);
        }

        // PRO / SEMI-PRO PYRAMID
        $pro = [
            ['NBA_WNBA','NBA / WNBA',10,1],
            ['G_LEAGUE','G-League (NBA Gatorade League)',20,2],
            ['OVERSEAS_TOP','Overseas Top Leagues (EuroLeague, ACB, CBA, NBL etc.)',30,3],
            ['FIBA_CLUBS','FIBA Americas / FIBA Europe Clubs',40,4],
            ['SEMI_PRO','Semi-Pro Leagues (CEBL, NBL Canada, ABA, TBL, etc.)',50,5],
            ['NATIONAL_TEAMS','Men’s / Women’s National Teams (Senior, U19, U17)',60,1], // keep high prestige
        ];
        foreach ($pro as $row) {
            $append('PRO_PYRAMID_BBALL', $row, ['pro_rel' => false]);
        }

        // ADULT COMMUNITY & CITY LEAGUES
        $adult = [
            ['AD_PREMIER','Premier',10,10],
            ['AD_DIV1','Division 1',20,20],
            ['AD_DIV2','Division 2',30,30],
            ['AD_DIV3','Division 3',40,40],
            ['AD_DIV4','Division 4',50,50],
            ['AD_COMP_A','Competitive A',60],
            ['AD_COMP_B','Competitive B',70],
            ['AD_COMP_C','Competitive C',80],
            ['AD_INTER_A','Intermediate A',90],
            ['AD_INTER_B','Intermediate B',100],
            ['AD_REC_A','Recreational A',110],
            ['AD_REC_B','Recreational B',120],
            ['AD_REC_C','Recreational C',130],
            ['AD_CORP','Corporate / Workplace League',140],
            ['AD_SUN','Sunday League',150],
        ];
        foreach ($adult as $row) {
            $append('ADULT_COMM', $row);
        }

        // MASTERS / O-AGE
        foreach (['O30'=>30,'O35'=>35,'O40'=>40,'O45'=>45,'O50'=>50,'O55'=>55,'O60'=>60] as $tag=>$age) {
            $payload[] = $opt('MASTERS','MASTERS_'.$tag,"Masters O{$age}",$age);
        }

        // TOURNAMENT & AAU FLIGHTS
        $flights = [
            'ELITE','PLATINUM','GOLD','SILVER','BRONZE','COPPER','OPEN','CHALLENGER','CHAMPIONSHIP','FLIGHT_1','FLIGHT_2','FLIGHT_3'
        ];
        $o=10; foreach ($flights as $tag) {
            $label = match($tag){
                'FLIGHT_1' => 'Flight 1',
                'FLIGHT_2' => 'Flight 2',
                'FLIGHT_3' => 'Flight 3',
                default    => ucwords(strtolower(str_replace('_',' ',$tag)))
            };
            $payload[] = $opt('TOURNEY_AAU','AAU_'.$tag,$label,$o); $o+=10;
        }

        // SPECIAL FORMATS & VARIATIONS
        $special = [
            ['SP_3X3','3x3 (FIBA 3×3, Olympic)',10,['variant'=>'3x3','court'=>'half']],
            ['SP_STREETBALL','Streetball / Blacktop',20],
            ['SP_HALF_3ON3','Half-Court 3-on-3',30,['variant'=>'3x3','court'=>'half']],
            ['SP_1ON1','1-on-1',40,['variant'=>'1v1','court'=>'half']],
            ['SP_WHEELCHAIR','Wheelchair Basketball – Junior, Senior, Open',50,['inclusive'=>true]],
            ['SP_SPEC_OLY','Special Olympics Divisions',60,['inclusive'=>true]],
            ['SP_UNIFIED','Unified / Inclusive Leagues',70,['inclusive'=>true]],
            ['SP_MIXED_ABILITY','Mixed-Ability / Adapted Hoops',80,['inclusive'=>true]],
            ['SP_MINI_BASKET','Mini-Basketball (lower rims, smaller balls)',90,['youth'=>true]],
        ];
        foreach ($special as $row) {
            $append('SPECIAL_VAR', $row);
        }

        // SEASONAL / INTRAMURAL
        $seasonal = ['Fall League','Winter League','Spring League','Summer League','Intramural A / B / C'];
        $o=10; foreach ($seasonal as $label) {
            $payload[] = $opt('SEASONAL_INTRAM','SEAS_'.strtoupper(preg_replace('/[^A-Z0-9]+/i','_',$label)),$label,$o); $o+=10;
        }

        // SKILL BANDS
        $bands = ['AAA','AA','A','B','C','D','E'];
        $o=10; foreach ($bands as $label) {
            $payload[] = $opt('SKILL_BAND','SKILL_'.$label,$label,$o, null, ['band'=>$label]);
            $o+=10;
        }

        // 3) Upsert all options
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
