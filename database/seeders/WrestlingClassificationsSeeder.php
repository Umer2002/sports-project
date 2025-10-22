<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WrestlingClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Wrestling sport_id
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['wrestling'])
            ->orWhere('name', 'WRESTLING')
            ->value('id');

        if (!$sportId) return;

        // 2) Define groups (code => [label, sort_order])
        $groups = [
            'WRS_YOUTH_INTRO'   => ['YOUTH / INTRO PROGRAMS', 10],
            'WRS_SCHOOL'        => ['SCHOOL / SCHOLASTIC (Folkstyle)', 20],
            'WRS_COLLEGE'       => ['COLLEGE / UNIVERSITY', 30],
            'WRS_NATL_INTL'     => ['NATIONAL & INTERNATIONAL (Freestyle / Greco-Roman)', 40],
            'WRS_PRO_ENT'       => ['PROFESSIONAL / ENTERTAINMENT (not Olympic style)', 50],

            // Weight-class groups
            'WRS_WT_HS'         => ['COMMON WEIGHT CLASSES — U.S. High School (Folkstyle)', 60],
            'WRS_WT_NCAA_M'     => ['COMMON WEIGHT CLASSES — NCAA (Men’s)', 61],
            'WRS_WT_WOMEN_COL'  => ['COMMON WEIGHT CLASSES — Women’s College / Freestyle (common)', 62],
            'WRS_WT_OLY_MFS'    => ['INTERNATIONAL OLYMPIC — Men’s Freestyle (kg)', 63],
            'WRS_WT_OLY_WFS'    => ['INTERNATIONAL OLYMPIC — Women’s Freestyle (kg)', 64],
            'WRS_WT_GRECO'      => ['INTERNATIONAL — Greco-Roman (kg)', 65],

            'WRS_CLUB_OPEN'     => ['CLUB / OPEN COMPETITION', 70],
            'WRS_SPECIAL'       => ['SPECIAL FORMATS & VARIANTS', 80],
            'WRS_SEASONAL'      => ['SEASONAL & INTRAMURAL', 90],
        ];

        // Upsert groups & capture IDs
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

        // Helper to add options
        $opt = function($groupCode, $code, $label, $order, $numericRank, $meta) use ($groupIds, $now) {
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
        $kgMeta  = fn($limitKg) => ['unit'=>'kg','limit'=>$limitKg];
        $lbMeta  = fn($limitLb) => ['unit'=>'lb','limit'=>$limitLb];

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

        // YOUTH / INTRO PROGRAMS
        $youth = [
            ['YI_TOT_FUN','Tot Wrestling / Fun Wrestling',10,10, ['intro'=>true]],
            ['YI_BEGINNER_FOLK','Beginner Folkstyle Leagues',20,20, ['style'=>'folkstyle']],
            ['YI_NOVICE_2Y','Novice (≤2 yrs experience)',30,30, ['threshold_years'=>2]],
            ['YI_INTERMEDIATE','Intermediate',40,40],
            ['YI_ADV_ELITE_TRAVEL','Advanced / Elite Youth Travel Teams',50,50, ['travel'=>true]],
        ];
        foreach ($youth as $row) {
            $append('WRS_YOUTH_INTRO', $row);
        }

        // SCHOOL / SCHOLASTIC (Folkstyle)
        $school = [
            ['SCH_ELEM','Elementary / Primary',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HS_FRESH','High School: Freshman',30],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',40],
            ['SCH_HS_VARS','High School: Varsity',50],
            ['SCH_PREP_PRIVATE','Prep / Private Academy Programs',60],
        ];
        foreach ($school as $row) {
            $append('WRS_SCHOOL', $row);
        }

        // COLLEGE / UNIVERSITY
        $college = [
            // United States
            ['US_NJCAA','United States: NJCAA (Junior College)',10,60, ['country'=>'US']],
            ['US_NAIA','United States: NAIA',20,55, ['country'=>'US']],
            ['US_NCAA_D3','United States: NCAA Division III',30,50, ['country'=>'US']],
            ['US_NCAA_D2','United States: NCAA Division II',40,40, ['country'=>'US']],
            ['US_NCAA_D1_MFOLK','United States: NCAA Division I (Men’s Folkstyle)',50,25, ['country'=>'US','gender'=>'men','style'=>'folkstyle']],
            ['US_NCWWC_WFS','United States: NCAA Women’s Emerging Sport / NCWWC (Freestyle rules)',60,30, ['country'=>'US','gender'=>'women','style'=>'freestyle']],
            // Canada
            ['CA_USPORTS','Canada: U Sports Wrestling (Men & Women)',70,28, ['country'=>'CA']],
            ['CA_CCAA','Canada: CCAA Colleges',80,48, ['country'=>'CA']],
        ];
        foreach ($college as $row) {
            $append('WRS_COLLEGE', $row);
        }

        // NATIONAL & INTERNATIONAL
        $intl = [
            ['INTL_CADET_U17','Cadet (U17)',10,80, ['u'=>'U17']],
            ['INTL_JUNIOR_U20','Junior (U20)',20,70, ['u'=>'U20']],
            ['INTL_U23','U23',30,60, ['u'=>'U23']],
            ['INTL_SENIOR_OPEN','Senior / Open',40,40, ['u'=>'Open']],
            ['INTL_NATIONALS','National Championships',50,35],
            ['INTL_PAN_AM','Pan-American Championships',60,25],
            ['INTL_WORLDS','World Championships',70,12],
            ['INTL_OLYMPICS','Olympic Games',80,10],
        ];
        foreach ($intl as $row) {
            $append('WRS_NATL_INTL', $row);
        }

        // PRO / ENTERTAINMENT
        $pro = [
            ['PRO_INDIES','Independent Pro Wrestling Promotions',10, null, ['entertainment'=>true]],
            ['PRO_MAJOR_TV','AEW, WWE, Impact, etc.',20, null, ['entertainment'=>true,'tv'=>true]],
            ['PRO_NOTE','(Performance-based, separate from amateur sport)',30, null, ['note'=>true]],
        ];
        foreach ($pro as $row) {
            $append('WRS_PRO_ENT', $row);
        }

        // WEIGHT CLASSES — High School (folkstyle, lb limits)
        $hs = [106,113,120,126,132,138,144,150,157,165,175,190,215,285];
        $rank = 10;
        foreach ($hs as $i => $lb) {
            $payload[] = $opt('WRS_WT_HS', 'HS_'.$lb, $lb.( $lb===106 ? ' lb' : ''), 10+($i*5), $rank+$i*5, $lbMeta($lb));
        }

        // WEIGHT CLASSES — NCAA Men (lb limits)
        $ncaaM = [125,133,141,149,157,165,174,184,197,285];
        $rank = 10;
        foreach ($ncaaM as $i => $lb) {
            $payload[] = $opt('WRS_WT_NCAA_M', 'NCAA_M_'.$lb, (string)$lb, 10+($i*5), $rank+$i*5, $lbMeta($lb));
        }

        // WEIGHT CLASSES — Women’s College / Freestyle (lb limits)
        $womenCol = [101,109,116,123,130,136,143,155,170,191];
        $rank = 10;
        foreach ($womenCol as $i => $lb) {
            $payload[] = $opt('WRS_WT_WOMEN_COL', 'WCOL_'.$lb, (string)$lb, 10+($i*5), $rank+$i*5, $lbMeta($lb));
        }

        // INTERNATIONAL OLYMPIC — Men’s Freestyle (kg)
        $menFsKg = [57,61,65,70,74,79,86,92,97,125];
        $rank = 10;
        foreach ($menFsKg as $i => $kg) {
            $payload[] = $opt('WRS_WT_OLY_MFS', 'MFS_'.$kg.'KG', $kg.' kg', 10+($i*5), $rank+$i*5, $kgMeta($kg));
        }

        // INTERNATIONAL OLYMPIC — Women’s Freestyle (kg)
        $womenFsKg = [50,53,55,57,59,62,65,68,72,76];
        $rank = 10;
        foreach ($womenFsKg as $i => $kg) {
            $payload[] = $opt('WRS_WT_OLY_WFS', 'WFS_'.$kg.'KG', $kg.' kg', 10+($i*5), $rank+$i*5, $kgMeta($kg));
        }

        // INTERNATIONAL — Greco-Roman (kg)
        $grecoKg = [55,60,63,67,72,77,82,87,97,130];
        $rank = 10;
        foreach ($grecoKg as $i => $kg) {
            $payload[] = $opt('WRS_WT_GRECO', 'GR_'.$kg.'KG', $kg.' kg', 10+($i*5), $rank+$i*5, $kgMeta($kg));
        }

        // CLUB / OPEN COMPETITION
        $club = [
            ['CLUB_LOCAL_OPEN','Local Open Tournaments',10,90],
            ['CLUB_REGION_STATE','Regional / State / Provincial Championships',20,60],
            ['CLUB_NATL_DUALS','National Club Duals',30,45, ['dual'=>true]],
            ['CLUB_FS_GR_QUAL','Freestyle & Greco State/Provincial Qualifiers',40,50],
            ['CLUB_BEACH','Beach Wrestling',50,70, ['surface'=>'beach']],
        ];
        foreach ($club as $row) {
            $append('WRS_CLUB_OPEN', $row);
        }

        // SPECIAL FORMATS & VARIANTS
        $special = [
            ['SP_FOLKSTYLE','Folkstyle (USA)',10, null, ['style'=>'folkstyle']],
            ['SP_FREESTYLE','Freestyle',20, null, ['style'=>'freestyle']],
            ['SP_GRECO','Greco-Roman',30, null, ['style'=>'greco']],
            ['SP_SUB_GRAPPLING','Submission Grappling / No-Gi',40, null, ['style'=>'nogi']],
            ['SP_BEACH','Beach Wrestling',50, null, ['surface'=>'beach']],
            ['SP_CATCH','Catch Wrestling',60],
            ['SP_SAMBO','Sambo (combat & sport)',70],
            ['SP_PRO_GRAPPLING','Professional Grappling Leagues (e.g., FloGrappling events)',80, null, ['league'=>true]],
            ['SP_PARA_DEAF','Para-Wrestling / Deaf Wrestling',90, null, ['inclusive'=>true]],
        ];
        foreach ($special as $row) {
            $append('WRS_SPECIAL', $row);
        }

        // SEASONAL & INTRAMURAL
        $seasonal = [
            ['SEAS_YOUTH_FW','Youth Fall/Winter Season (folkstyle)',10, null, ['season'=>'fall/winter','style'=>'folkstyle']],
            ['SEAS_SS_FS_GR','Spring/Summer Freestyle & Greco Season',20, null, ['season'=>'spring/summer','style'=>'freestyle/greco']],
            ['SEAS_COL_INTRAM','College Intramural / Club Wrestling',30],
            ['SEAS_CORP_CHARITY','Corporate / Charity Grappling Events',40, null, ['charity'=>true]],
        ];
        foreach ($seasonal as $row) {
            $append('WRS_SEASONAL', $row);
        }

        // 4) Upsert all options
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
