<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SwimmingClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Swimming sport_id (be forgiving on name/case)
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['swimming', 'swim'])
            ->orWhereIn('name', ['SWIMMING', 'Swimming'])
            ->value('id');

        if (!$sportId) return;

        // 2) Define groups (code => [name, sort_order])
        $groups = [
            'SWM_DEV'        => ['LEARN-TO-SWIM & DEVELOPMENT', 10],
            'SWM_AGE_DIV'    => ['AGE-GROUP COMPETITIVE DIVISIONS', 20],
            'SWM_SCHOOL'     => ['SCHOOL / SCHOLASTIC', 30],
            'SWM_COLLEGE'    => ['COLLEGE / UNIVERSITY', 40],
            'SWM_CLUB_NAT'   => ['CLUB / REGIONAL / NATIONAL COMPETITION', 50],
            'SWM_INTL'       => ['INTERNATIONAL / ELITE', 60],
            'SWM_SPECIAL'    => ['SPECIALTY EVENTS / FORMATS', 70],
            'SWM_PARA'       => ['PARA-SWIMMING CLASSIFICATIONS (World Para Swimming)', 80],
            'SWM_SEASONAL'   => ['SEASONAL & INTRAMURAL', 90],
        ];

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

        // LEARN-TO-SWIM & DEVELOPMENT
        $dev = [
            ['DEV_PARENT_TOT','Parent & Tot',10, 10, ['intro'=>true,'age'=>'18mo-3y']],
            ['DEV_PRESCHOOL','Preschool / Tiny Tot',20, 20, ['intro'=>true,'age'=>'3-5']],
            ['DEV_SAFETY','Water Safety / Survival',30, 30, ['badge'=>'safety']],
            ['DEV_PRECOMP','Pre-Competitive',40, 50, ['path'=>'pre-comp']],
            ['DEV_STROKE_LVLS','Stroke Development (Levels 1-6 or Bronze/Silver/Gold)',50, 60, ['levels'=>['1','2','3','4','5','6'],'alt'=>['Bronze','Silver','Gold']]],
            ['DEV_JUN_DEV_SQUAD','Junior Development Squad',60, 70],
        ];
        foreach ($dev as $row) {
            $append('SWM_DEV', $row);
        }

        // AGE-GROUP COMPETITIVE DIVISIONS (USA Swimming & Swimming Canada style)
        $age = [
            ['AGE_U8','8 & Under (8U)',10, 10, ['u'=>'8U']],
            ['AGE_U10','10 & Under (10U)',20, 20, ['u'=>'10U']],
            ['AGE_11_12','11–12',30, 30, ['u'=>'11-12']],
            ['AGE_13_14','13–14',40, 40, ['u'=>'13-14']],
            ['AGE_15_16','15–16',50, 50, ['u'=>'15-16']],
            ['AGE_17_18','17–18',60, 60, ['u'=>'17-18']],
            ['AGE_SENIOR_OPEN','Senior / Open',70, 100, ['u'=>'Open']],
        ];
        foreach ($age as $row) {
            $append('SWM_AGE_DIV', $row);
        }

        // SCHOOL / SCHOLASTIC
        $school = [
            ['SCH_ELEM','Elementary / Primary Swim Meets',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HS_FRESH','High School: Freshman',30],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',40],
            ['SCH_HS_VARS','High School: Varsity',50],
        ];
        foreach ($school as $row) {
            $append('SWM_SCHOOL', $row);
        }

        // COLLEGE / UNIVERSITY (USA & CANADA)
        $collegeUS = [
            ['US_NJCAA','United States: NJCAA (Junior College)',10,60],
            ['US_NAIA','United States: NAIA',20,55],
            ['US_NCAA_D3','United States: NCAA Division III',30,50],
            ['US_NCAA_D2','United States: NCAA Division II',40,40],
            ['US_NCAA_D1','United States: NCAA Division I',50,20],
        ];
        foreach ($collegeUS as $row) {
            $append('SWM_COLLEGE', $row);
        }

        $collegeCA = [
            ['CA_USPORTS','Canada: U Sports Swimming',60,25, ['country'=>'CA']],
            ['CA_CCAA','Canada: CCAA Colleges',70,45, ['country'=>'CA']],
            ['CA_UNI_CLUB','Canada: University Club Teams',80,65, ['country'=>'CA','club'=>true]],
        ];
        foreach ($collegeCA as $row) {
            $append('SWM_COLLEGE', $row);
        }

        // CLUB / REGIONAL / NATIONAL COMPETITION
        $clubNat = [
            ['CLUB_LOCAL_DUAL','Local Club Dual Meets',10,90],
            ['CLUB_LSC','Regional / LSC Championships',20,70],
            ['CLUB_STATE_PROV','State / Provincial Championships',30,60],
            ['CLUB_SECTIONALS','Sectionals',40,50],
            ['CLUB_JNATS','Junior Nationals',50,30],
            ['CLUB_SNATS','Senior Nationals',60,20],
            ['CLUB_TRIALS','National Trials (Olympic/Worlds)',70,10],
        ];
        foreach ($clubNat as $row) {
            $append('SWM_CLUB_NAT', $row);
        }

        // INTERNATIONAL / ELITE
        $intl = [
            ['INTL_PAN_AM','Pan-American Games',10,30],
            ['INTL_COMMONWEALTH','Commonwealth Games',20,25],
            ['INTL_WORLD_AQUA','World Aquatics Championships',30,12],
            ['INTL_OLYMPICS','Olympic Games',40,10],
            ['INTL_FINA_WC','FINA World Cup Circuit',50,20],
            ['INTL_ISL','Professional Swim League / ISL',60,22],
        ];
        foreach ($intl as $row) {
            $append('SWM_INTL', $row);
        }

        // SPECIALTY EVENTS / FORMATS
        $special = [
            ['SP_SCY_25Y','Short-Course Yards (25 yd)',10, null, ['course'=>'SCY','length_yards'=>25]],
            ['SP_SCM_25M','Short-Course Meters (25 m)',20, null, ['course'=>'SCM','length_m'=>25]],
            ['SP_LCM_50M','Long-Course Meters (50 m)',30, null, ['course'=>'LCM','length_m'=>50]],
            ['SP_OPEN_WATER','Open Water 5 km / 10 km / Marathon 25 km',40, null, ['discipline'=>'open_water','distances_km'=>[5,10,25]]],
            ['SP_RELAY_TEAMS','Relay Teams (Free, Medley)',50, null, ['relays'=>['Free','Medley']]],
            ['SP_MIXED_RELAYS','Mixed-Gender Relays',60, null, ['relays'=>'mixed']],
            ['SP_ARTISTIC','Synchronized / Artistic Swimming',70, null, ['discipline'=>'artistic']],
            ['SP_MASTERS_OW','Masters Open Water',80, null, ['masters'=>true,'discipline'=>'open_water']],
            ['SP_PARA_FLAG','Para-Swimming (see classes below)',90, null, ['para'=>true]],
        ];
        foreach ($special as $row) {
            $append('SWM_SPECIAL', $row);
        }

        // PARA-SWIMMING CLASSIFICATIONS
        $para = [
            ['PARA_S1_S10','S1–S10: Physical Impairment (Freestyle, Back, Butterfly)',10, null, ['classes'=>['S1','S2','S3','S4','S5','S6','S7','S8','S9','S10']]],
            ['PARA_SB1_SB9','SB1–SB9: Breaststroke',20, null, ['classes'=>['SB1','SB2','SB3','SB4','SB5','SB6','SB7','SB8','SB9']]],
            ['PARA_SM1_SM10','SM1–SM10: Individual Medley',30, null, ['classes'=>['SM1','SM2','SM3','SM4','SM5','SM6','SM7','SM8','SM9','SM10']]],
            ['PARA_S11_S13','S11–S13: Visual Impairment',40, null, ['classes'=>['S11','S12','S13']]],
            ['PARA_S14','S14: Intellectual Impairment',50, null, ['classes'=>['S14']]],
        ];
        foreach ($para as $row) {
            $append('SWM_PARA', $row);
        }

        // SEASONAL & INTRAMURAL
        $seasonal = [
            ['SEAS_SUMMER_LEAGUE','Summer League (Recreational “summer swim”)',10],
            ['SEAS_WINTER_CLUB','Winter Club Season',20],
            ['SEAS_COL_INTRAM','College Intramural Meets',30],
            ['SEAS_CORP_CHARITY','Corporate / Charity Swim Meets',40],
        ];
        foreach ($seasonal as $row) {
            $append('SWM_SEASONAL', $row);
        }

        // 4) Upsert
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
