<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GymnasticsClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Gymnastics sport_id
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['gymnastics'])
            ->orWhere('name', 'GYMNASTICS')
            ->value('id');

        if (!$sportId) return;

        // 2) Groups
        $groups = [
            'GYM_REC'        => ['RECREATIONAL & DEVELOPMENT', 10],
            'GYM_WAG'        => ['WOMEN’S ARTISTIC GYMNASTICS (WAG – USA/Canada)', 20],
            'GYM_MAG'        => ['MEN’S ARTISTIC GYMNASTICS (MAG – USA/Canada)', 30],
            'GYM_RHYTHMIC'   => ['RHYTHMIC GYMNASTICS', 40],
            'GYM_TT'         => ['TRAMPOLINE & TUMBLING (T&T)', 50],
            'GYM_ACRO'       => ['ACROBATIC GYMNASTICS', 60],
            'GYM_PARKOUR'    => ['PARKOUR (recognized by FIG/USAG)', 70],
            'GYM_SCHOOL'     => ['SCHOOL / SCHOLASTIC', 80],
            'GYM_COLLEGE'    => ['COLLEGE / UNIVERSITY', 90],
            'GYM_ADULT'      => ['ADULT & MASTERS COMPETITION', 100],
            'GYM_SPECIAL'    => ['SPECIAL FORMATS & VARIANTS', 110],
            'GYM_FLIGHTS'    => ['TOURNAMENT / FLIGHT LABELS (often used in meets)', 120],
            'GYM_SEASONAL'   => ['SEASONAL & INTRAMURAL', 130],
        ];

        $groupIds = [];
        foreach ($groups as $code => [$name, $order]) {
            DB::table('sport_classification_groups')->updateOrInsert(
                ['sport_id' => $sportId, 'code' => $code],
                ['name' => $name, 'sort_order' => $order, 'created_at' => $now, 'updated_at' => $now]
            );
            $groupIds[$code] = DB::table('sport_classification_groups')
                ->where(['sport_id'=>$sportId,'code'=>$code])->value('id');
        }

        // helper
        $opt = function($g, $code, $label, $order, $rank, $meta) use ($groupIds, $now) {
            return [
                'group_id'     => $groupIds[$g],
                'code'         => $code,
                'label'        => $label,
                'sort_order'   => $order,
                'numeric_rank' => $rank,
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

        // 3) RECREATIONAL & DEVELOPMENT
        $rec = [
            ['REC_PARENT_CHILD','Parent-Child / Kindergym',10,10, ['intro'=>true]],
            ['REC_GENERAL','Recreational / General Gymnastics',20,20],
            ['REC_FUND','Fundamentals / Beginner',30,30],
            ['REC_INTERMEDIATE','Intermediate',40,40],
            ['REC_ADV','Advanced Rec',50,50],
            ['REC_XCEL','Xcel (USAG: Bronze, Silver, Gold, Platinum, Diamond, Sapphire)',60,60, ['xcel'=>['Bronze','Silver','Gold','Platinum','Diamond','Sapphire']]],
            ['REC_CANGYM','CanGym (Canada badge levels)',70,65, ['canada'=>true,'badges'=>true]],
            ['REC_ADULT','Adult Recreational',80,80, ['adult'=>true]],
        ];
        foreach ($rec as $row) {
            $append('GYM_REC', $row);
        }

        // 4) WAG
        $wag = [
            ['WAG_PRETEAM','Pre-Team / Pre-Comp',10,20],
            ['WAG_DP_L1_10','Development Program Levels 1–10',20,40, ['levels'=>range(1,10)]],
            ['WAG_HOPES_ELITE','Elite / Hopes (Pre-Elite)',30,30, ['elite'=>true,'hopes'=>true]],
            ['WAG_NATIONAL_TEAM','National Team / Senior Elite',40,15, ['national'=>true]],
            ['WAG_OWC','Olympic / World Championships',50,10, ['global'=>true]],
        ];
        foreach ($wag as $row) {
            $append('GYM_WAG', $row);
        }

        // 5) MAG
        $mag = [
            ['MAG_L1_10','Levels 1–10',10,40, ['levels'=>range(1,10)]],
            ['MAG_JUNIOR_ELITE','Junior Elite',20,30],
            ['MAG_SENIOR_ELITE','Senior Elite / National Team',30,15, ['national'=>true]],
            ['MAG_OWC','Olympic / World Championships',40,10, ['global'=>true]],
        ];
        foreach ($mag as $row) {
            $append('GYM_MAG', $row);
        }

        // 6) RHYTHMIC
        $rh = [
            ['RG_L1_10','Levels 1–10',10,40],
            ['RG_GRP_IND','Group / Individual',20,35, ['divisions'=>['Group','Individual']]],
            ['RG_JR_SR_ELITE','Junior / Senior Elite',30,20],
            ['RG_NAT_WORLD_OLY','National / World / Olympic',40,10, ['global'=>true]],
        ];
        foreach ($rh as $row) {
            $append('GYM_RHYTHMIC', $row);
        }

        // 7) TRAMPOLINE & TUMBLING (T&T)
        $tt = [
            ['TT_L1_10','Levels 1–10',10,50],
            ['TT_AGE_GROUP','Development / Age Group (AG) 11-12, 13-14, 15-16, 17+',20,40, ['age_groups'=>['11-12','13-14','15-16','17+']]],
            ['TT_ELITE','Elite',30,20, ['elite'=>true]],
            ['TT_SYNC_TRAMP','Synchronized Trampoline',40,35, ['event'=>'synchro']],
            ['TT_DMT','Double Mini',50,30, ['event'=>'DMT']],
            ['TT_POWER_TUMBLE','Power Tumbling',60,25, ['event'=>'tumbling']],
        ];
        foreach ($tt as $row) {
            $append('GYM_TT', $row);
        }

        // 8) ACROBATIC GYMNASTICS
        $acro = [
            ['ACRO_L5_ELITE','Levels 5–Elite',10,40],
            ['ACRO_PAIR_TRIO_GROUP','Pair / Trio / Group (Women’s Pair, Men’s Pair, Mixed Pair, Women’s Group, Men’s Group)',20,30,
                ['divisions'=>["Women's Pair","Men's Pair","Mixed Pair","Women's Group","Men's Group"]]],
            ['ACRO_AGE_GROUPS','Age Groups: 11–16, 12–18, 13–19, Senior',30,20, ['age_groups'=>['11–16','12–18','13–19','Senior']]],
        ];
        foreach ($acro as $row) {
            $append('GYM_ACRO', $row);
        }

        // 9) PARKOUR
        $pk = [
            ['PK_BEGIN_INT_ADV','Beginner / Intermediate / Advanced',10,60],
            ['PK_FREESTYLE_SPEED','Freestyle / Speed Divisions',20,40, ['divisions'=>['Freestyle','Speed']]],
            ['PK_NAT_INTL_ELITE','National & International Elite',30,20, ['elite'=>true]],
        ];
        foreach ($pk as $row) {
            $append('GYM_PARKOUR', $row);
        }

        // 10) SCHOOL / SCHOLASTIC
        $school = [
            ['SCH_ELEM','Elementary / Primary Gym Clubs',10],
            ['SCH_MIDDLE','Middle School Teams',20],
            ['SCH_HS_FRESH','High School: Freshman',30],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',40],
            ['SCH_HS_VARS','High School: Varsity',50],
        ];
        foreach ($school as $row) {
            $append('GYM_SCHOOL', $row);
        }

        // 11) COLLEGE / UNIVERSITY
        $college = [
            ['US_NCAA_WOMEN','NCAA Women’s Gymnastics (Div I, II, III)',10,20, ['country'=>'US','gender'=>'women']],
            ['US_NCAA_MEN','NCAA Men’s Gymnastics (limited Div I)',20,25, ['country'=>'US','gender'=>'men']],
            ['US_NAIGC','Club / NAIGC',30,40, ['club'=>true]],
            ['CA_USPORTS','U Sports University Gymnastics Clubs/Teams',40,30, ['country'=>'CA']],
        ];
        foreach ($college as $row) {
            $append('GYM_COLLEGE', $row);
        }

        // 12) ADULT & MASTERS
        $adult = [
            ['AD_MASTERS_MEETS','Adult Artistic / Masters Meets',10,40, ['adult'=>true]],
            ['AD_GFA','Gymnastics for All (FIG)',20,50, ['gfa'=>true]],
            ['AD_TEAMGYM','TeamGym (group floor routines)',30,30, ['teamgym'=>true]],
            ['AD_MASTERS_AGE','Masters Age Groups 25+, 30+, 35+, … 60+',40,60, ['masters'=>[25,30,35,40,45,50,55,60]]],
        ];
        foreach ($adult as $row) {
            $append('GYM_ADULT', $row);
        }

        // 13) SPECIAL FORMATS & VARIANTS
        $special = [
            ['SP_TEAMGYM','TeamGym (Europe/NA exhibitions)',10, null, ['teamgym'=>true]],
            ['SP_GYMFEST','GymFest / Gymnaestrada (non-competitive)',20, null, ['festival'=>true]],
            ['SP_ACRO_YOGA','Acro Yoga / Performance Troupes',30, null, ['performance'=>true]],
            ['SP_AERIAL_CIRCUS','Aerial / Circus Arts cross-training',40, null, ['circus'=>true]],
            ['SP_ADAPTIVE_PARA','Adaptive / Para Gymnastics (inclusive programs)',50, null, ['inclusive'=>true]],
        ];
        foreach ($special as $row) {
            $append('GYM_SPECIAL', $row);
        }

        // 14) TOURNAMENT / FLIGHT LABELS
        $flights = [
            ['FLT_BRONZE','Bronze',10,80],
            ['FLT_SILVER','Silver',20,60],
            ['FLT_GOLD','Gold',30,40],
            ['FLT_PLATINUM','Platinum',40,30],
            ['FLT_DIAMOND','Diamond',50,20],
            ['FLT_ELITE','Elite',60,10],
            ['FLT_OPEN','Open / Championship',70,15],
            ['FLT_FLIGHT_1','Flight 1',80,90],
            ['FLT_FLIGHT_2','Flight 2',90,92],
            ['FLT_FLIGHT_3','Flight 3',100,94],
        ];
        foreach ($flights as $row) {
            $append('GYM_FLIGHTS', $row);
        }

        // 15) SEASONAL & INTRAMURAL
        $seasonal = [
            ['SEAS_FALL_COMP','Fall Compulsory Season',10, null, ['season'=>'fall','type'=>'compulsory']],
            ['SEAS_SPRING_OPT','Spring Optional Season',20, null, ['season'=>'spring','type'=>'optional']],
            ['SEAS_SUMMER_CAMPS','Summer Camps / Training',30, null, ['season'=>'summer','camp'=>true]],
            ['SEAS_COL_INTRAM','College Intramural / Club Meets',40],
        ];
        foreach ($seasonal as $row) {
            $append('GYM_SEASONAL', $row);
        }

        // 16) Upsert
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
