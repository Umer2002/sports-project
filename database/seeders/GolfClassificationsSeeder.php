<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GolfClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Golf sport_id (case/alias tolerant)
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['golf'])
            ->orWhere('name', 'GOLF')
            ->value('id');

        if (!$sportId) return;

        // 2) Groups (code => [label, sort_order])
        $groups = [
            'GOLF_INTRO'      => ['INTRO & DEVELOPMENT', 10],
            'GOLF_JUNIOR'     => ['JUNIOR COMPETITION', 20],
            'GOLF_SCHOOL'     => ['SCHOOL / SCHOLASTIC', 30],
            'GOLF_COLLEGE'    => ['COLLEGE / UNIVERSITY', 40],
            'GOLF_ADULT_AM'   => ['ADULT & SENIOR AMATEUR', 50],
            'GOLF_PRO'        => ['PROFESSIONAL & ELITE', 60],
            'GOLF_FORMATS'    => ['TOURNAMENT FORMATS (often used as divisions)', 70],
            'GOLF_SPECIAL'    => ['SPECIAL PROGRAMS & VARIANTS', 80],
            'GOLF_SEASONAL'   => ['SEASONAL & INTRAMURAL', 90],
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

        // INTRO & DEVELOPMENT
        $intro = [
            ['IN_FIRST_TEE','First Tee / Junior Golf Programs',10,10, ['junior'=>true]],
            ['IN_LEARN_CLINICS','Learn-to-Golf Clinics',20,20, ['clinic'=>true]],
            ['IN_BEGINNER_LEAGUES','Beginner / Recreational Leagues',30,30],
            ['IN_PARENT_CHILD','Parent-Child / Family Scramble Events',40,35, ['team'=>true,'format'=>'scramble']],
        ];
        foreach ($intro as $row) {
            $append('GOLF_INTRO', $row);
        }

        // JUNIOR COMPETITION (approx rank: local → world)
        $junior = [
            ['JR_PGA_LEAGUE','PGA Jr. League',10,60, ['junior'=>true]],
            ['JR_USKIDS_LOCAL','U.S. Kids Golf Local Tours',20,70, ['junior'=>true]],
            ['JR_DCP','Drive, Chip & Putt',30,55, ['skills'=>['drive','chip','putt']]],
            ['JR_REG_TOURS','Regional/State Junior Tours (AJGA, CJGA, IMG, Hurricane)',40,40],
            ['JR_PROV_STATE_CHAMPS','Provincial / State Junior Championships',50,35],
            ['JR_NATIONALS','National Junior Championships',60,25],
            ['JR_RYDER_PRES_CUP','Junior Ryder Cup / Junior Presidents Cup',70,18, ['team'=>true]],
            ['JR_WORLD_JR','World Junior Championships',80,12, ['global'=>true]],
        ];
        foreach ($junior as $row) {
            $append('GOLF_JUNIOR', $row);
        }

        // SCHOOL / SCHOLASTIC
        $school = [
            ['SCH_MIDDLE','Middle School Golf',10],
            ['SCH_HS_FRESH','High School: Freshman',20],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',30],
            ['SCH_HS_VARS','High School: Varsity',40],
            ['SCH_PREP_ACAD','Prep / Academy Golf Teams',50],
        ];
        foreach ($school as $row) {
            $append('GOLF_SCHOOL', $row);
        }

        // COLLEGE / UNIVERSITY (USA & Canada)
        $collegeUS = [
            ['US_NJCAA','United States: NJCAA (Junior College)',10,60],
            ['US_NAIA','United States: NAIA',20,55],
            ['US_NCAA_D3','United States: NCAA Division III',30,50],
            ['US_NCAA_D2','United States: NCAA Division II',40,40],
            ['US_NCAA_D1','United States: NCAA Division I (Men & Women)',50,20],
        ];
        foreach ($collegeUS as $row) {
            $append('GOLF_COLLEGE', $row);
        }

        $collegeCA = [
            ['CA_USPORTS','Canada: U Sports Golf',60,25, ['country'=>'CA']],
            ['CA_CCAA','Canada: CCAA Colleges',70,45, ['country'=>'CA']],
            ['CA_INTER_UNI','Canada: Inter-University Club Teams',80,65, ['country'=>'CA','club'=>true]],
        ];
        foreach ($collegeCA as $row) {
            $append('GOLF_COLLEGE', $row);
        }

        // ADULT & SENIOR AMATEUR
        $adult = [
            ['AD_MUNI_PUBLIC','Municipal / Public-Course Leagues',10,90],
            ['AD_CLUB_M_W','Club Men’s / Women’s League',20,80],
            ['AD_SCRATCH','Scratch League',30,40, ['scratch'=>true]],
            ['AD_MIXED','Mixed League',40,85, ['mixed'=>true]],
            ['AD_STATE_PROV_AM','State / Provincial Amateur Championships',50,35],
            ['AD_MID_AM','Mid-Amateur (typically 25+ or 30+)',60,50, ['age_min'=>25]],
            ['AD_SENIOR_AM','Senior Amateur (50+ and 55+ divisions)',70,55, ['age_min'=>50]],
            ['AD_SUPER_SENIOR','Super Senior (60+, 65+, etc.)',80,60, ['age_min'=>60]],
            ['AD_CLUB_CHAMPS','Club Championships (Flights: Championship, A, B, C)',90,45, ['flights'=>['Championship','A','B','C']]],
            ['AD_HANDICAP_DIV','Handicap Divisions (0-5, 6-10, 11-15, 16-20, 21+)',100,65, ['handicap_bands'=>['0-5','6-10','11-15','16-20','21+']]],
        ];
        foreach ($adult as $row) {
            $append('GOLF_ADULT_AM', $row);
        }

        // PROFESSIONAL & ELITE (rough prestige rank)
        $pro = [
            ['PRO_PGA_TOUR','PGA TOUR (and FedExCup events)',10,12, ['tour'=>'PGA']],
            ['PRO_KORN_FERRY','Korn Ferry Tour',20,30, ['tour'=>'KFT']],
            ['PRO_PGA_AMERICAS','PGA TOUR Canada / PGA TOUR Americas',30,40],
            ['PRO_PGA_CHAMPIONS','PGA TOUR Champions (50+)',40,35, ['age_min'=>50]],
            ['PRO_LPGA','LPGA Tour',50,18, ['tour'=>'LPGA']],
            ['PRO_EPSON','Epson Tour (LPGA feeder)',60,45, ['tour'=>'Epson']],
            ['PRO_LET','LET (events in NA)',70,38, ['tour'=>'LET']],
            ['PRO_MAJORS','Major Championships: Masters, PGA, U.S. Open, Open Championship',80,5, ['majors'=>true]],
            ['PRO_WGC','WGC Events',90,10],
            ['PRO_OLYMPIC','Olympic Golf',100,8],
            ['PRO_TEAM_CUPS','Presidents Cup / Ryder Cup / Solheim Cup',110,9, ['team'=>true]],
        ];
        foreach ($pro as $row) {
            $append('GOLF_PRO', $row);
        }

        // TOURNAMENT FORMATS
        $formats = [
            ['FMT_STROKE','Stroke Play (Gross / Net)',10, null, ['stroke'=>true]],
            ['FMT_MATCH','Match Play',20, null, ['match'=>true]],
            ['FMT_STABLEFORD','Stableford',30],
            ['FMT_SCRAMBLE','Scramble (2-man, 4-man)',40, null, ['team'=>true,'variants'=>['2-man','4-man']]],
            ['FMT_BEST_BALL','Best Ball / Four-Ball',50, null, ['team'=>true]],
            ['FMT_ALT_SHOT','Alternate Shot / Foursomes',60, null, ['team'=>true]],
            ['FMT_SHAMBLE','Shamble',70],
            ['FMT_SKINS','Skins',80],
            ['FMT_SHOOTOUT','Shootout',90],
            ['FMT_PRO_AM','Pro-Am',100, null, ['pro_am'=>true]],
            ['FMT_CLUB_FLIGHTS','Club Flights: Championship, A, B, C (handicap-based)',110, null, ['flights'=>['Championship','A','B','C']]],
        ];
        foreach ($formats as $row) {
            $append('GOLF_FORMATS', $row);
        }

        // SPECIAL PROGRAMS & VARIANTS
        $special = [
            ['SP_PAR3','Par-3 Leagues',10],
            ['SP_NIGHT','Night Golf / Glow Golf',20, null, ['night'=>true]],
            ['SP_SIMULATOR','Indoor Simulator Leagues',30, null, ['indoor'=>true]],
            ['SP_FOOTGOLF','FootGolf (hybrid)',40, null, ['hybrid'=>'footgolf']],
            ['SP_DISC_GOLF','Disc Golf (if cross-listed by clubs)',50, null, ['hybrid'=>'disc_golf']],
            ['SP_ADAPTIVE','Adaptive/Para Golf Divisions (WR4GD, US Adaptive Open)',60, null, ['inclusive'=>true]],
            ['SP_SPECIAL_OLY','Special Olympics Golf',70, null, ['inclusive'=>true]],
            ['SP_LONG_DRIVE','Long-Drive Competitions',80, null, ['long_drive'=>true]],
        ];
        foreach ($special as $row) {
            $append('GOLF_SPECIAL', $row);
        }

        // SEASONAL & INTRAMURAL
        $seasonal = [
            ['SEAS_SPRING','Spring League',10],
            ['SEAS_SUMMER','Summer League',20],
            ['SEAS_FALL','Fall League',30],
            ['SEAS_WINTER_INDOOR','Winter Indoor League',40, null, ['indoor'=>true]],
            ['SEAS_COL_INTRAM','College Intramural A / B / C',50],
            ['SEAS_CORP_CHARITY','Corporate / Charity Tournaments',60, null, ['charity'=>true]],
        ];
        foreach ($seasonal as $row) {
            $append('GOLF_SEASONAL', $row);
        }

        // 4) Upsert all options
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
