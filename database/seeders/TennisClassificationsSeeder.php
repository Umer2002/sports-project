<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TennisClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Tennis sport_id (case/alias tolerant)
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['tennis'])
            ->orWhere('name', 'TENNIS')
            ->value('id');

        if (!$sportId) return;

        // 2) Groups (code => [name, sort_order])
        $groups = [
            'TEN_DEV_REC'     => ['DEVELOPMENT / RECREATIONAL PATHWAY', 10],
            'TEN_JUNIOR'      => ['JUNIOR COMPETITION', 20],
            'TEN_SCHOOL'      => ['SCHOOL / SCHOLASTIC', 30],
            'TEN_COLLEGE'     => ['COLLEGE / UNIVERSITY', 40],
            'TEN_ADULT'       => ['ADULT & SENIOR COMPETITION', 50],
            'TEN_PRO'         => ['PROFESSIONAL & ELITE', 60],
            'TEN_DOUBLES'     => ['DOUBLES & TEAM FORMATS', 70],
            'TEN_SPECIAL'     => ['SPECIAL FORMATS & VARIANTS', 80],
            'TEN_FLIGHTS'     => ['TOURNAMENT FLIGHTS / SKILL BANDS', 90],
            'TEN_SEASONAL'    => ['SEASONAL & INTRAMURAL', 100],
        ];

        // Upsert groups & fetch IDs
        $groupIds = [];
        foreach ($groups as $code => [$name, $order]) {
            DB::table('sport_classification_groups')->updateOrInsert(
                ['sport_id' => $sportId, 'code' => $code],
                ['name' => $name, 'sort_order' => $order, 'created_at' => $now, 'updated_at' => $now]
            );
            $groupIds[$code] = DB::table('sport_classification_groups')
                ->where(['sport_id' => $sportId, 'code' => $code])->value('id');
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

        // DEVELOPMENT / RECREATIONAL PATHWAY
        $dev = [
            ['DEV_L2P_QS','Learn to Play / QuickStart / Progressive Tennis',10,20, ['intro'=>true]],
            ['DEV_COMM_PARKS','Community / Parks & Recreation Programs',20,30],
            ['DEV_HOUSE_LADDER','House League / Club Ladder',30,40],
            ['DEV_LOCAL_JR_CIRCUIT','Local Junior Circuit (entry-level tournaments)',40,50, ['junior'=>true]],
        ];
        foreach ($dev as $row) {
            $append('TEN_DEV_REC', $row);
        }

        // JUNIOR COMPETITION
        $jr = [
            ['JR_U10_PROGRESS','10 & Under (Red/Orange/Green Ball progression)',10,90, ['balls'=>['Red','Orange','Green']]],
            ['JR_U12','12 & Under (U12)',20,80, ['u'=>'U12']],
            ['JR_U14','14 & Under (U14)',30,70, ['u'=>'U14']],
            ['JR_U16','16 & Under (U16)',40,60, ['u'=>'U16']],
            ['JR_U18','18 & Under (U18)',50,50, ['u'=>'U18']],
            ['JR_NATIONALS','National Junior Championships',60,30],
            ['JR_GRAND_SLAMS','Junior Grand Slams (U18)',70,20],
            ['JR_ITF_WORLD','ITF World Junior Tour',80,15],
        ];
        foreach ($jr as $row) {
            $append('TEN_JUNIOR', $row);
        }

        // SCHOOL / SCHOLASTIC
        $school = [
            ['SCH_ELEM','Elementary / Primary',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HS_FRESH','High School: Freshman',30],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',40],
            ['SCH_HS_VARS','High School: Varsity',50],
            ['SCH_PREP_ACAD','Prep / Academy Teams',60],
        ];
        foreach ($school as $row) {
            $append('TEN_SCHOOL', $row);
        }

        // COLLEGE / UNIVERSITY
        $collegeUS = [
            ['US_NJCAA','United States: NJCAA (Junior College)',10,60],
            ['US_NAIA','United States: NAIA',20,55],
            ['US_NCAA_D3','United States: NCAA Division III',30,50],
            ['US_NCAA_D2','United States: NCAA Division II',40,40],
            ['US_NCAA_D1','United States: NCAA Division I',50,20],
        ];
        foreach ($collegeUS as $row) {
            $append('TEN_COLLEGE', $row);
        }

        $collegeCA = [
            ['CA_USPORTS','Canada: U Sports University Tennis',60,25, ['country'=>'CA']],
            ['CA_CCAA','Canada: CCAA Colleges',70,45, ['country'=>'CA']],
            ['CA_INTER_UNI_CLUB','Canada: Inter-University Club Leagues',80,65, ['country'=>'CA','club'=>true]],
        ];
        foreach ($collegeCA as $row) {
            $append('TEN_COLLEGE', $row);
        }

        // ADULT & SENIOR COMPETITION
        $adult = [
            ['AD_USTA_TC_LEAGUES','USTA / Tennis Canada Adult Leagues (NTRP ratings 1.0–7.0)',10, null, ['rating_system'=>'NTRP','range'=>'1.0-7.0']],
            ['AD_USTA_DIV_25','USTA League Division 2.5',20, null, ['ntrp'=>2.5]],
            ['AD_USTA_DIV_30','USTA League Division 3.0',30, null, ['ntrp'=>3.0]],
            ['AD_USTA_DIV_35','USTA League Division 3.5',40, null, ['ntrp'=>3.5]],
            ['AD_USTA_DIV_40','USTA League Division 4.0',50, null, ['ntrp'=>4.0]],
            ['AD_USTA_DIV_45','USTA League Division 4.5',60, null, ['ntrp'=>4.5]],
            ['AD_USTA_DIV_50','USTA League Division 5.0',70, null, ['ntrp'=>5.0]],
            ['AD_USTA_DIV_55P','USTA League Division 5.5+',80, null, ['ntrp'=>'5.5+']],
            ['AD_REG_PROV','Regional / Provincial Championships',90,40],
            ['AD_NAT_ADULT','National Adult Championships',100,30],
            ['AD_MASTERS_AGE','Masters Age Groups (25+, 30+, 35+ … 90+)',110,50, ['masters'=>true]],
            ['AD_OPEN_MONEY','Open / Money Tournaments',120,20],
        ];
        foreach ($adult as $row) {
            $append('TEN_ADULT', $row);
        }

        // PROFESSIONAL & ELITE
        $pro = [
            ['PRO_ATP_250','ATP Tour (250)',10,50, ['tour'=>'ATP','tier'=>250]],
            ['PRO_ATP_500','ATP Tour (500)',20,40, ['tour'=>'ATP','tier'=>500]],
            ['PRO_ATP_1000','ATP Tour (Masters 1000)',30,25, ['tour'=>'ATP','tier'=>1000]],
            ['PRO_ATP_FINALS','ATP Finals',40,15, ['tour'=>'ATP','finals'=>true]],
            ['PRO_WTA_250','WTA Tour (250)',50,50, ['tour'=>'WTA','tier'=>250]],
            ['PRO_WTA_500','WTA Tour (500)',60,40, ['tour'=>'WTA','tier'=>500]],
            ['PRO_WTA_1000','WTA Tour (1000)',70,25, ['tour'=>'WTA','tier'=>1000]],
            ['PRO_WTA_FINALS','WTA Finals',80,15, ['tour'=>'WTA','finals'=>true]],
            ['PRO_ITF_MW','ITF World Tennis Tour (M15/M25, W15/W25)',90,70, ['tour'=>'ITF']],
            ['PRO_GRAND_SLAMS','Grand Slam Events (Australian, Roland Garros, Wimbledon, US Open)',100,10, ['grand_slam'=>true]],
            ['PRO_DAVIS','Davis Cup (Men’s National Teams)',110,20],
            ['PRO_BJKC','Billie Jean King Cup (Women’s National Teams)',120,20],
            ['PRO_LAVER_UNITED','Laver Cup / United Cup',130,30],
            ['PRO_OLY_PANAM','Olympic & Pan-American Games Tennis',140,18],
        ];
        foreach ($pro as $row) {
            $append('TEN_PRO', $row);
        }

        // DOUBLES & TEAM FORMATS
        $dbl = [
            ['DB_MEN','Men’s Doubles',10, null, ['doubles'=>true,'gender'=>'men']],
            ['DB_WOMEN','Women’s Doubles',20, null, ['doubles'=>true,'gender'=>'women']],
            ['DB_MIXED','Mixed Doubles',30, null, ['doubles'=>true,'gender'=>'mixed']],
            ['DB_COLLEGE_TEAM','College Team Tennis (dual matches)',40, null, ['team'=>true]],
            ['DB_WTT','World TeamTennis (WTT)',50, null, ['team'=>true,'league'=>'WTT']],
            ['DB_JTT','Junior Team Tennis (JTT)',60, null, ['junior'=>true,'team'=>true]],
            ['DB_INTERCLUB','Inter-club / Inter-district Team Competitions',70, null, ['team'=>true]],
        ];
        foreach ($dbl as $row) {
            $append('TEN_DOUBLES', $row);
        }

        // SPECIAL FORMATS & VARIANTS
        $special = [
            ['SP_WHEELCHAIR','Wheelchair Tennis (ITF divisions: Open, Quad, Juniors)',10, null, ['inclusive'=>true,'itf'=>['Open','Quad','Juniors']]],
            ['SP_BEACH','Beach Tennis (ITF)',20, null, ['surface'=>'beach']],
            ['SP_CARDIO_PICKLE','Pickleball-Tennis Hybrids / Cardio Tennis (recreational)',30, null, ['recreational'=>true]],
            ['SP_FAST4','Fast4 / Short-Set Tennis',40, null, ['format'=>'Fast4']],
            ['SP_TB_TENS','Tie-Break Tens',50, null, ['format'=>'TB10']],
            ['SP_TOUCH_MINI','TouchTennis / Mini-Tennis',60, null, ['mini'=>true]],
            ['SP_CORP_CHARITY','Corporate / Charity Tennis',70, null, ['charity'=>true]],
        ];
        foreach ($special as $row) {
            $append('TEN_SPECIAL', $row);
        }

        // TOURNAMENT FLIGHTS / SKILL BANDS
        $flights = [
            ['FLT_BEGINNER','Beginner / Novice',10,90],
            ['FLT_INTERMEDIATE','Intermediate',20,70],
            ['FLT_ADVANCED','Advanced',30,40],
            ['FLT_ELITE_OPEN','Elite / Open',40,20],
            ['FLT_BRONZE','Bronze',50,80],
            ['FLT_SILVER','Silver',60,60],
            ['FLT_GOLD','Gold',70,40],
            ['FLT_PLATINUM','Platinum',80,30],
            ['FLT_AAA','AAA',90,25],
            ['FLT_AA','AA',100,35],
            ['FLT_A','A',110,45],
        ];
        foreach ($flights as $row) {
            $append('TEN_FLIGHTS', $row);
        }

        // SEASONAL & INTRAMURAL
        $seasonal = [
            ['SEAS_INDOOR_WINTER','Indoor Winter League',10],
            ['SEAS_OUTDOOR_SUMMER','Outdoor Summer League',20],
            ['SEAS_COL_INTRAM','College Intramural A / B / C',30],
            ['SEAS_CORP_SOCIAL','Corporate / Social Ladders',40],
        ];
        foreach ($seasonal as $row) {
            $append('TEN_SEASONAL', $row);
        }

        // 4) Upsert all options
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
