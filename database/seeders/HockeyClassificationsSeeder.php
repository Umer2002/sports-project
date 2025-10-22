<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HockeyClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Hockey sport_id (your table uses "HOCKEY")
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['hockey','ice hockey'])
            ->orWhereIn('name', ['HOCKEY','Ice Hockey'])
            ->value('id');

        if (!$sportId) return; // no Hockey row found

        // 2) Groups (code => [name, sort_order])
        $groups = [
            'INTRO_HKY'        => ['LEARN-TO-PLAY / INTRO', 10],
            'MINOR_HKY'        => ['MINOR HOCKEY (Canada/USA Common Names)', 20],
            'TRAD_AGE_HKY'     => ['TRADITIONAL AGE-CLASS LABELS (still widely used)', 30],
            'YTH_TOURNEY_HKY'  => ['YOUTH TOURNAMENT FLIGHTS', 40],
            'HS_HKY'           => ['HIGH-SCHOOL HOCKEY', 50],
            'JUNIOR_HKY'       => ['JUNIOR HOCKEY', 60],
            'COLLEGE_HKY'      => ['COLLEGE / UNIVERSITY', 70],
            'ADULT_SENIOR_HKY' => ['ADULT & SENIOR HOCKEY', 80],
            'PRO_HKY'          => ['PROFESSIONAL HOCKEY', 90],
            'SPECIAL_HKY'      => ['SPECIAL FORMATS & VARIANTS', 100],
            'SEASONAL_INTR_HKY'=> ['SEASONAL / INTRAMURAL', 110],
            'SKILL_BAND_HKY'   => ['SKILL / COMPETITIVE BANDS (used across many leagues)', 120],
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

        // INTRO
        $intro = [
            ['INTRO_LEARN_SKATE','Learn to Skate',10, null, ['intro'=>true]],
            ['INTRO_LEARN_PLAY','Learn to Play Hockey',20, null, ['intro'=>true]],
            ['INTRO_MINI_MITE','Mini-Mite',30],
            ['INTRO_INITIATION','Initiation (Canada: IP or U7)',40, null, ['canada'=>true]],
            ['INTRO_CROSS_HALF','Cross-Ice / Half-Ice',50, null, ['ice'=>'cross/half']],
        ];
        foreach ($intro as $row) {
            $append('INTRO_HKY', $row);
        }

        // MINOR HOCKEY (tiers/streams)
        $minor = [
            ['MINOR_HOUSE','House / Local / Recreational',10,90],
            ['MINOR_SELECT_DEV','Select / Development / Local League',20,80],
            ['MINOR_A','A (Single-A)',30,70],
            ['MINOR_AA','AA (Double-A)',40,60],
            ['MINOR_AAA','AAA (Triple-A / Elite)',50,50],
            ['MINOR_MINOR_MAJOR','Minor / Major designations (e.g., Minor Peewee, Major Peewee)',60],
        ];
        foreach ($minor as $row) {
            $append('MINOR_HKY', $row);
        }

        // TRADITIONAL AGE-CLASS
        $trad = [
            ['AGE_MITE_U8','Mite (U8)',10, null, ['u'=>'U8']],
            ['AGE_SQUIRT_U10','Squirt (U10)',20, null, ['u'=>'U10']],
            ['AGE_PEEWEE_U12','Peewee (U12)',30, null, ['u'=>'U12']],
            ['AGE_BANTAM_U14','Bantam (U14)',40, null, ['u'=>'U14']],
            ['AGE_MIDGET_U18','Midget (U18)',50, null, ['u'=>'U18']],
            ['AGE_JUVENILE_U21','Juvenile (U21)',60, null, ['u'=>'U21']],
        ];
        foreach ($trad as $row) {
            $append('TRAD_AGE_HKY', $row);
        }

        // YOUTH TOURNAMENT FLIGHTS
        $yfl = [
            ['YF_BRONZE','Bronze',10,60],
            ['YF_SILVER','Silver',20,50],
            ['YF_GOLD','Gold',30,40],
            ['YF_PLATINUM','Platinum',40,30],
            ['YF_ELITE','Elite',50,25],
            ['YF_AAA_T1','AAA Elite / Tier 1',60,10],
            ['YF_AA_T2','AA / Tier 2',70,20],
            ['YF_A_T3','A / Tier 3',80,30],
            ['YF_OPEN_CHAMP','Open / Championship',90,70],
            ['YF_FLIGHT_1','Flight 1',100,80],
            ['YF_FLIGHT_2','Flight 2',110,90],
            ['YF_FLIGHT_3','Flight 3',120,100],
        ];
        foreach ($yfl as $row) {
            $append('YTH_TOURNEY_HKY', $row);
        }

        // HIGH SCHOOL
        $hs = [
            ['HS_FRESHMAN','Freshman',10],
            ['HS_JV','Junior Varsity (JV)',20],
            ['HS_VARSITY','Varsity',30],
            ['HS_PREP_ACAD','Prep / Academy',40],
        ];
        foreach ($hs as $row) {
            $append('HS_HKY', $row);
        }

        // JUNIOR HOCKEY
        $junior = [
            // Canada
            ['JR_CAN_C','Canada: Junior C',10,70, ['country'=>'CA']],
            ['JR_CAN_B','Canada: Junior B',20,60, ['country'=>'CA']],
            ['JR_CAN_A','Canada: Junior A (OJHL, BCHL, AJHL, etc.)',30,50, ['country'=>'CA']],
            ['JR_CAN_MAJOR_CHL','Canada: Major Junior (CHL – OHL, WHL, QMJHL)',40,20, ['country'=>'CA']],
            // USA
            ['JR_US_T3','USA: Tier III (USPHL, NA3HL)',50,70, ['country'=>'US']],
            ['JR_US_T2','USA: Tier II (NAHL)',60,40, ['country'=>'US']],
            ['JR_US_T1','USA: Tier I (USHL)',70,25, ['country'=>'US']],
            // Women’s U22
            ['JR_CAN_FEMALE_U22','Canadian Junior Female U22 (e.g., PWHL U22)',80,35, ['female'=>true]],
        ];
        foreach ($junior as $row) {
            $append('JUNIOR_HKY', $row);
        }

        // COLLEGE / UNIVERSITY
        $college = [
            ['COL_NCAA_D3','NCAA Division III',10,50],
            ['COL_NCAA_D2','NCAA Division II',20,40],
            ['COL_NCAA_D1','NCAA Division I',30,20],
            ['COL_ACHA_D1','ACHA Div I (club)',40,70],
            ['COL_ACHA_D2','ACHA Div II (club)',50,80],
            ['COL_ACHA_D3','ACHA Div III (club)',60,90],
            ['COL_U_SPORTS','U Sports (Canada)',70,25],
            ['COL_ACHA_WOM_D1','ACHA Women’s Div I',80,60, ['female'=>true]],
            ['COL_ACHA_WOM_D2','ACHA Women’s Div II',90,70, ['female'=>true]],
        ];
        foreach ($college as $row) {
            $append('COLLEGE_HKY', $row);
        }

        // ADULT & SENIOR
        $adult = [
            ['AD_SENIOR_AAA','Senior AAA (Allan Cup)',10,10, ['canada'=>true]],
            ['AD_SENIOR_AA_A_B','Senior AA / A / B',20,20],
            ['AD_SENIOR_REC','Senior Rec Leagues (Open, A, B, C, D)',30,60],
            ['AD_BEER_MENS','Beer League / Men’s League',40,80],
            ['AD_MASTERS','Masters O30 / O35 / O40 / O45 / O50+',50,40, ['masters_bands'=>['O30','O35','O40','O45','O50+']]],
            ['AD_WOMENS_SR_REC','Women’s Senior / Rec',60,50, ['female'=>true]],
            ['AD_INDUSTRIAL','Industrial / Corporate / Draft Leagues',70,70],
        ];
        foreach ($adult as $row) {
            $append('ADULT_SENIOR_HKY', $row);
        }

        // PROFESSIONAL
        $pro = [
            ['PRO_NHL','National Hockey League (NHL)',10,1],
            ['PRO_AHL','American Hockey League (AHL)',20,5],
            ['PRO_ECHL','ECHL',30,10],
            ['PRO_SPHL_FPHL','SPHL / FPHL',40,20],
            ['PRO_EURO','European Leagues (KHL, SHL, Liiga, etc. with NA players)',50,8],
            ['PRO_PWHL','Professional Women’s Hockey League (PWHL)',60,6, ['female'=>true]],
            ['PRO_MINOR_INDEP','Minor/Independent Pro (FPHL, LNAH)',70,25],
        ];
        foreach ($pro as $row) {
            $append('PRO_HKY', $row);
        }

        // SPECIAL FORMATS & VARIANTS
        $special = [
            ['SP_3V3_HALF','3-on-3 Half-Ice',10, null, ['format'=>'3v3','ice'=>'half']],
            ['SP_3V3_CROSS','3-on-3 Cross-Ice Youth',20, null, ['format'=>'3v3','ice'=>'cross']],
            ['SP_3ICE_PRO','3ICE Professional 3-on-3',30, null, ['format'=>'3v3','pro'=>true]],
            ['SP_BALL_DEK','Ball / Dek / Street Hockey',40, null, ['surface'=>'outdoor/inline']],
            ['SP_INLINE_ROLLER','Inline / Roller Hockey (Pro, Amateur, NARCh, PIHA)',50, null, ['inline'=>true]],
            ['SP_POND_OUTDOOR','Pond Hockey / Outdoor Classic',60, null, ['outdoor'=>true]],
            ['SP_SLEDGE_PARA','Sledge / Para Ice Hockey',70, null, ['inclusive'=>true]],
            ['SP_BLIND_DEAF_SPECIAL','Blind / Deaf / Special Hockey',80, null, ['inclusive'=>true]],
            ['SP_FLOOR_COSOM','Floor Hockey / Cosom',90],
            ['SP_RINGETTE','Ringette (closely related, often under same associations)',100, null, ['related'=>'ringette']],
        ];
        foreach ($special as $row) {
            $append('SPECIAL_HKY', $row);
        }

        // SEASONAL / INTRAMURAL
        $seasonal = [
            ['SEAS_FALL','Fall League',10],
            ['SEAS_WINTER','Winter League',20],
            ['SEAS_SPRING_AAA','Spring AAA',30],
            ['SEAS_SUMMER_AAA','Summer AAA',40],
            ['SEAS_INTRAMURAL','College Intramural A / B / C',50],
        ];
        foreach ($seasonal as $row) {
            $append('SEASONAL_INTR_HKY', $row);
        }

        // SKILL BANDS
        $bands = [
            ['AAA','AAA',10,10],
            ['AA','AA',20,20],
            ['A','A',30,30],
            ['BB','BB',40,40],
            ['B','B',50,50],
            ['C','C',60,60],
            ['D','D',70,70],
            ['REC_BEGINNER','Rec / Beginner',80,90],
        ];
        foreach ($bands as [$code,$label,$order,$rank]) {
            $payload[] = $opt('SKILL_BAND_HKY','SKILL_'.$code,$label,$order,$rank,['band'=>$label]);
        }

        // 4) Upsert all options
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
