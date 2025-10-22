<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoccerClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Find Soccer sport_id (fallbacks by name)
        $soccerId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['soccer','football (soccer)'])
            ->orWhere('name', 'Soccer')
            ->value('id');

        if (!$soccerId) {
            // Try a few more
            $soccerId = DB::table('sports')->whereIn('name', ['Soccer','Soccer'])->value('id');
        }
        if (!$soccerId) return; // no Soccer found, skip

        // 2) Define groups (code => [name, order])
        $groups = [
            'MATCH_FORMAT'   => ['MATCH FORMATS', 10],
            'REC_LEVEL'      => ['RECREATIONAL / COMMUNITY LEVELS', 20],
            'COMP_TIER'      => ['COMPETITIVE YOUTH / AMATEUR TIERS (COMMON LABELS)', 30],
            'PREPRO'         => ['PRE-PROFESSIONAL / SEMI-PRO (COMMON LABELS)', 40],
            'PRO_PYRAMID'    => ['PROFESSIONAL PYRAMID (GENERIC)', 50],
            'SCHOOL_PATH'    => ['SCHOOL / CAMPUS PATHWAYS', 60],
            'ADULT_COMM'     => ['ADULT COMMUNITY LEAGUES (TYPICAL BREAKDOWN)', 70],
            'MASTERS'        => ['MASTERS / O-AGE BRACKETS', 80],
            'TOURNEY_FLIGHT' => ['TOURNAMENT FLIGHTS / BRACKETS (COMMON NAMING)', 90],
            'SPECIAL'        => ['SPECIAL FORMATS & PROGRAMS', 100],
            'INCLUSIVE'      => ['INCLUSIVE / ADAPTED Soccer (EXAMPLES)', 110],
            'OFF_SEASON'     => ['OFF-SEASON / ALTERNATIVE', 120],
            'SKILL_BAND'     => ['SKILL-BAND LABELS (OFTEN USED IN LEAGUES)', 130],
            'DEV_STAGE'      => ['OFFICIAL DEVELOPMENT STAGES (GENERIC WORDING YOU’LL SEE)', 140],
        ];

        // Upsert groups
        $groupIds = [];
        foreach ($groups as $code => [$name, $order]) {
            DB::table('sport_classification_groups')->updateOrInsert(
                ['sport_id' => $soccerId, 'code' => $code],
                ['name' => $name, 'sort_order' => $order, 'updated_at' => $now, 'created_at' => $now]
            );
            $groupIds[$code] = DB::table('sport_classification_groups')
                ->where(['sport_id' => $soccerId, 'code' => $code])
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

        // 3) Options per group
        $payload = [];

        // MATCH FORMAT (add team_size/surface in meta when relevant)
        $formats = [
            ['FMT_3V3','3v3',10,['team_size'=>3]],
            ['FMT_4V4','4v4',20,['team_size'=>4]],
            ['FMT_5V5_FUTSAL','5v5 (Futsal)',30,['team_size'=>5,'variant'=>'futsal','surface'=>'indoor']],
            ['FMT_6V6','6v6',40,['team_size'=>6]],
            ['FMT_7V7','7v7',50,['team_size'=>7]],
            ['FMT_8V8','8v8',60,['team_size'=>8]],
            ['FMT_9V9','9v9',70,['team_size'=>9]],
            ['FMT_11V11','11v11',80,['team_size'=>11]],
            ['FMT_BEACH_5V5','Beach Soccer (5v5)',90,['team_size'=>5,'surface'=>'beach']],
            ['FMT_WALKING','Walking Soccer (varies)',100,['variant'=>'walking']],
        ];
        foreach ($formats as [$code,$label,$order,$meta]) {
            $payload[] = $opt('MATCH_FORMAT',$code,$label,$order,null,$meta);
        }

        // RECREATIONAL / COMMUNITY
        $rec = [
            'REC_INTRO' => ['Intro / Grassroots',10],
            'REC_ACTIVE_START' => ['Learn to Play / Active Start',20],
            'REC_HOUSE' => ['House League',30],
            'REC_COMMUNITY' => ['Community League',40],
            'REC_C' => ['Recreational C',50],
            'REC_B' => ['Recreational B',60],
            'REC_A' => ['Recreational A',70],
            'REC_DEV' => ['Developmental (Rec+)',80],
            'REC_ALL_STAR' => ['All-Star (Rec Select)',90],
        ];
        foreach ($rec as $code => [$label,$order]) {
            $payload[] = $opt('REC_LEVEL',$code,$label,$order);
        }

        // COMPETITIVE TIERS — include numeric_rank to sort divisions/tiers cleanly
        $tiers = [
            // Named pathways
            ['TIER_SELECT','Select',10,50],
            ['TIER_DEVELOPMENT','Development',20,40],
            ['TIER_ACADEMY','Academy',30,30],
            ['TIER_ELITE_ACADEMY','Elite Academy',40,20],
            // Tier numbers (lower rank = higher level)
            ['TIER_4','Tier 4',50,40],
            ['TIER_3','Tier 3',60,30],
            ['TIER_2','Tier 2',70,20],
            ['TIER_1','Tier 1',80,10],
            // Division 10…1 (rank 100 down to 10)
            ['DIV_10','Division 10',110,100],
            ['DIV_9','Division 9',120,90],
            ['DIV_8','Division 8',130,80],
            ['DIV_7','Division 7',140,70],
            ['DIV_6','Division 6',150,60],
            ['DIV_5','Division 5',160,50],
            ['DIV_4','Division 4',170,40],
            ['DIV_3','Division 3',180,30],
            ['DIV_2','Division 2',190,20],
            ['DIV_1','Division 1',200,10],
            // Metals etc. (approximate ranks)
            ['BRONZE','Bronze',210,70],
            ['SILVER','Silver',220,60],
            ['SILVER_ELITE','Silver Elite',230,55],
            ['GOLD','Gold',240,50],
            ['GOLD_ELITE','Gold Elite',250,45],
            ['PLATINUM','Platinum',260,40],
            ['DIAMOND','Diamond',270,30],
            ['PREMIER','Premier',280,20],
            ['CHAMPIONSHIP','Championship',290,25],
            ['FIRST_DIV','First Division',300,30],
            ['SECOND_DIV','Second Division',310,40],
            ['THIRD_DIV','Third Division',320,50],
            ['FOURTH_DIV','Fourth Division',330,60],
            ['DISTRICT','District',340,80],
            ['REGIONAL','Regional',350,70],
            ['PROV_STATE','Provincial / State',360,60],
            ['INTER_REGIONAL','Inter-Regional',370,50],
            ['NATIONAL','National',380,10],
        ];
        foreach ($tiers as [$code,$label,$order,$rank]) {
            $payload[] = $opt('COMP_TIER',$code,$label,$order,$rank);
        }

        // PRE-PRO / SEMI-PRO
        $prepro = [
            ['PREPRO_U20_U21_ELITE','U20 / U21 Elite',10],
            ['PREPRO_U23_ELITE','U23 Elite',20],
            ['PREPRO_RESERVE','Reserve League / “B” Teams',30],
            ['PREPRO_PREPRO','Pre-Professional',40],
            ['PREPRO_SEMIPRO','Semi-Professional',50],
            ['PREPRO_AM_PREM','Amateur Premier',60],
        ];
        foreach ($prepro as [$code,$label,$order]) {
            $payload[] = $opt('PREPRO',$code,$label,$order);
        }

        // PRO PYRAMID
        $pro = [
            ['PRO_L1','Level 1 / Top Flight / Premier Division',10,1],
            ['PRO_L2','Level 2 / Second Division',20,2],
            ['PRO_L3','Level 3 / Third Division',30,3],
            ['PRO_L4','Level 4 / Fourth Division',40,4],
            ['PRO_L5','Level 5 / Fifth Division',50,5],
        ];
        foreach ($pro as [$code,$label,$order,$rank]) {
            $payload[] = $opt('PRO_PYRAMID',$code,$label,$order,$rank,['pro_rel'=>true]);
        }

        // SCHOOL / CAMPUS
        $school = [
            ['SCH_ELEMENTARY','Elementary / Primary School',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HIGH','High School: Freshman / JV / Varsity',30],
            ['SCH_COL_CLUB','College / University: Club',40],
            ['SCH_COL_VARSITY','College / University: Varsity',50],
        ];
        foreach ($school as [$code,$label,$order]) {
            $payload[] = $opt('SCHOOL_PATH',$code,$label,$order);
        }

        // ADULT COMMUNITY
        $adult = [
            ['AD_PREMIER','Premier',10],
            ['AD_DIV1','Division 1',20,10],
            ['AD_DIV2','Division 2',30,20],
            ['AD_DIV3','Division 3',40,30],
            ['AD_DIV4','Division 4',50,40],
            ['AD_DIV5','Division 5',60,50],
            ['AD_DIV6','Division 6',70,60],
            ['AD_COMP_A','Competitive A',80],
            ['AD_COMP_B','Competitive B',90],
            ['AD_COMP_C','Competitive C',100],
            ['AD_INTER_A','Intermediate A',110],
            ['AD_INTER_B','Intermediate B',120],
            ['AD_REC_A','Recreational A',130],
            ['AD_REC_B','Recreational B',140],
            ['AD_REC_C','Recreational C',150],
        ];
        foreach ($adult as $row) {
            [$code, $label, $order, $rank, $meta] = array_pad($row, 5, null);
            if ($meta === null && is_array($rank)) {
                $meta = $rank;
                $rank = null;
            }

            $payload[] = $opt('ADULT_COMM', $code, $label, $order, $rank, $meta);
        }

        // MASTERS
        foreach ([
            'O30'=>30,'O35'=>35,'O40'=>40,'O45'=>45,'O50'=>50,'O55'=>55,'O60'=>60
        ] as $tag=>$age) {
            $payload[] = $opt('MASTERS', 'MASTERS_'.$tag, "Masters O{$age}", $age);
        }

        // TOURNAMENT FLIGHTS
        $flights = [
            'SUPER_GROUP','ELITE','PREMIER','GOLD','SILVER','BRONZE','COPPER',
            'PLATINUM','DIAMOND','FLIGHT_1','FLIGHT_2','FLIGHT_3','OPEN','CHALLENGER','CHAMPIONSHIP'
        ];
        $order = 10;
        foreach ($flights as $tag) {
            $payload[] = $opt('TOURNEY_FLIGHT', 'FLT_'.$tag, str_replace('_',' ', $tag==='FLIGHT_1'?'Flight 1':($tag==='FLIGHT_2'?'Flight 2':($tag==='FLIGHT_3'?'Flight 3':ucwords(strtolower($tag))))), $order);
            $order += 10;
        }

        // SPECIAL FORMATS & PROGRAMS
        $special = [
            ['SP_FUTSAL_5V5','Futsal 5v5 (Rec, Competitive, Elite)',10],
            ['SP_INDOOR_BOARDED','Indoor Boarded (6v6 / 7v7)',20],
            ['SP_ARENA_DOME','Arena / Dome Leagues',30],
            ['SP_SMALL_SIDED_FEST','Small-Sided Festivals (U6–U9)',40],
            ['SP_ACADEMY_POOLS','Academy Pools / Discovery',50],
            ['SP_HPTID','High-Performance / Talent ID',60],
            ['SP_RTC_PSP','Regional Training Centre / Provincial / State Program',70],
            ['SP_NTC_NDP','National Training Centre / National Development',80],
        ];
        foreach ($special as [$code,$label,$order]) {
            $payload[] = $opt('SPECIAL',$code,$label,$order);
        }

        // INCLUSIVE / ADAPTED
        $inclusive = [
            ['INC_PARA_CP','Para Soccer – CP (Cerebral Palsy)',10],
            ['INC_BLIND_5','Blind / 5-a-side',20],
            ['INC_AMPUTEE','Amputee',30],
            ['INC_DEAF_FUTSAL','Deaf Futsal',40],
            ['INC_POWERCHAIR','Powerchair Football',50],
            ['INC_UNIFIED','Unified / Inclusive Programs',60],
        ];
        foreach ($inclusive as [$code,$label,$order]) {
            $payload[] = $opt('INCLUSIVE',$code,$label,$order);
        }

        // OFF-SEASON / ALTERNATIVE
        $off = [
            'Summer League','Winter League','Fall League','Spring League',
            'Sunday League','Corporate / Workplace League','Intramural A / B / C'
        ];
        $order = 10;
        foreach ($off as $label) {
            $payload[] = $opt('OFF_SEASON','OFF_'.strtoupper(preg_replace('/[^A-Z0-9]+/i','_',$label)),$label,$order);
            $order += 10;
        }

        // SKILL BANDS
        $skills = ['AAA','AA','A','BB','B','C','D','E'];
        $order = 10;
        foreach ($skills as $label) {
            $payload[] = $opt('SKILL_BAND','SKILL_'.$label,$label,$order, null, ['band'=>$label]);
            $order += 10;
        }

        // DEVELOPMENT STAGES
        $dev = [
            'Active Start','FUNdamentals','Learn to Train','Train to Train',
            'Train to Compete','Train to Win','Soccer for Life'
        ];
        $order = 10;
        foreach ($dev as $label) {
            $payload[] = $opt('DEV_STAGE','DEV_'.strtoupper(str_replace(' ','_',$label)),$label,$order);
            $order += 10;
        }

        // 4) Upsert all options
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
