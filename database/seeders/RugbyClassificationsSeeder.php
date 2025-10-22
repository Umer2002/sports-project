<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RugbyClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['rugby', 'rugby union', 'rugby league'])
            ->orWhereIn('name', ['Rugby', 'RUGBY'])
            ->value('id');

        if (!$sportId) {
            return;
        }

        $groups = [
            'RUG_INTRO'      => ['INTRO & MINI RUGBY', 10],
            'RUG_YOUTH'      => ['YOUTH / AGE-GRADE PATHWAY', 20],
            'RUG_SCHOOL'     => ['SCHOOL / SCHOLASTIC', 30],
            'RUG_CLUB'       => ['CLUB & DOMESTIC COMPETITIONS', 40],
            'RUG_COLLEGE'    => ['COLLEGE / UNIVERSITY', 50],
            'RUG_PRO'        => ['PROFESSIONAL & ELITE', 60],
            'RUG_FORMATS'    => ['SPECIAL FORMATS & VARIANTS', 70],
            'RUG_SEASONAL'   => ['SEASONAL & FESTIVAL PLAY', 80],
            'RUG_SKILL_BAND' => ['SKILL / COMPETITIVE BANDS', 90],
        ];

        $groupIds = [];
        foreach ($groups as $code => [$label, $order]) {
            DB::table('sport_classification_groups')->updateOrInsert(
                ['sport_id' => $sportId, 'code' => $code],
                ['name' => $label, 'sort_order' => $order, 'created_at' => $now, 'updated_at' => $now]
            );

            $groupIds[$code] = DB::table('sport_classification_groups')
                ->where(['sport_id' => $sportId, 'code' => $code])
                ->value('id');
        }

        $opt = function (string $groupCode, string $code, string $label, int $order, ?int $rank = null, ?array $meta = null) use ($groupIds, $now) {
            return [
                'group_id'     => $groupIds[$groupCode],
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

        // INTRO & MINI RUGBY
        $intro = [
            ['INTRO_TAG','Tag / Touch Rugby (non-contact)',10, null, ['contact'=>'non']],
            ['INTRO_FLAG','Flag Rugby',20, null, ['contact'=>'non']],
            ['INTRO_MINI','Mini Rugby (U6-U8)',30, null, ['ages'=>['U6','U7','U8']]],
            ['INTRO_RUGBY_READY','Rugby Ready / Rookie Rugby',40, null, ['program'=>'rookie']],
        ];
        foreach ($intro as $row) {
            $append('RUG_INTRO', $row);
        }

        // YOUTH / AGE-GRADE
        $youth = [
            ['YTH_U10','U10',10,80],
            ['YTH_U12','U12',20,70],
            ['YTH_U14','U14',30,60],
            ['YTH_U16','U16',40,50],
            ['YTH_U18','U18',50,40],
            ['YTH_ACADEMY','Regional / Provincial Academy',60,30],
            ['YTH_HP','High Performance / Junior National',70,20],
        ];
        foreach ($youth as $row) {
            $append('RUG_YOUTH', $row);
        }

        // SCHOOL / SCHOLASTIC
        $school = [
            ['SCH_MIDDLE','Middle School',10],
            ['SCH_HS_FRESH','High School: Freshman / Jr. Varsity',20],
            ['SCH_HS_VARS','High School: Varsity',30],
            ['SCH_PREP','Prep / Academy',40],
        ];
        foreach ($school as $row) {
            $append('RUG_SCHOOL', $row);
        }

        // CLUB & DOMESTIC COMPETITIONS
        $club = [
            ['CLUB_SOCIAL','Social / Community Club',10,90],
            ['CLUB_SENIOR_MEN','Senior Men',20,60],
            ['CLUB_SENIOR_WOMEN','Senior Women',30,60, ['gender'=>'women']],
            ['CLUB_RESERVE','Reserve / 2nd XV',40,70],
            ['CLUB_DIV3','Division 3',50,80],
            ['CLUB_DIV2','Division 2',60,50],
            ['CLUB_DIV1','Division 1',70,40],
            ['CLUB_PREMIER','Premier / Elite Club',80,20],
            ['CLUB_PROV_PREM','Provincial Premiership',90,25],
        ];
        foreach ($club as $row) {
            $append('RUG_CLUB', $row);
        }

        // COLLEGE / UNIVERSITY
        $college = [
            ['COL_CLUB','College Club (D2/D3 / Small College)',10,80],
            ['COL_D1_ELITE','College Elite / D1-A',20,40],
            ['COL_VAR_MEN','Varsity Men',30,35, ['gender'=>'men']],
            ['COL_VAR_WOMEN','Varsity Women',40,35, ['gender'=>'women']],
            ['COL_CCNC','Collegiate Championships / Nationals',50,20],
        ];
        foreach ($college as $row) {
            $append('RUG_COLLEGE', $row);
        }

        // PROFESSIONAL & ELITE
        $pro = [
            ['PRO_MLR','Major League Rugby (MLR)',10,10, ['region'=>'North America']],
            ['PRO_PREMIERSHIP','Gallagher Premiership (England)',20,8, ['region'=>'UK']],
            ['PRO_URC','United Rugby Championship',30,7, ['region'=>'Europe']],
            ['PRO_TOP14','Top 14 (France)',40,6, ['region'=>'France']],
            ['PRO_SUPER_RUGBY','Super Rugby (Pacific)',50,5, ['region'=>'Pacific']],
            ['PRO_SIX_NATIONS','Six Nations',60,4, ['international'=>true]],
            ['PRO_RUGBY_CHAMP','Rugby Championship',70,3, ['international'=>true]],
            ['PRO_WORLD_CUP','Rugby World Cup',80,1, ['international'=>true]],
            ['PRO_WXV','WXV / Women’s Elite Competitions',90,2, ['gender'=>'women','international'=>true]],
        ];
        foreach ($pro as $row) {
            $append('RUG_PRO', $row);
        }

        // SPECIAL FORMATS & VARIANTS
        $formats = [
            ['FMT_SEVENS','Rugby Sevens',10, null, ['variant'=>'7s']],
            ['FMT_TENS','Rugby Tens',20, null, ['variant'=>'10s']],
            ['FMT_FIFTEENS','Traditional XVs',30, null, ['variant'=>'15s']],
            ['FMT_TOUCH','Touch Rugby',40, null, ['contact'=>'minimal']],
            ['FMT_FLAG','Flag Rugby',50, null, ['contact'=>'non']],
            ['FMT_RUGBY_LEAGUE','Rugby League',60, null, ['code'=>'league']],
            ['FMT_WHEELCHAIR','Wheelchair Rugby',70, null, ['inclusive'=>true]],
            ['FMT_BEACH','Beach Rugby',80, null, ['surface'=>'beach']],
        ];
        foreach ($formats as $row) {
            $append('RUG_FORMATS', $row);
        }

        // SEASONAL & FESTIVAL PLAY
        $seasonal = [
            ['SEAS_FALL','Fall League',10],
            ['SEAS_SPRING','Spring League',20],
            ['SEAS_SUMMER_7S','Summer Sevens Circuit',30, null, ['variant'=>'7s']],
            ['SEAS_TOUR_FEST','Tour / Festival Teams',40, null, ['festival'=>true]],
            ['SEAS_CORP_SOCIAL','Corporate / Social Touch Tournament',50, null, ['contact'=>'minimal']],
        ];
        foreach ($seasonal as $row) {
            $append('RUG_SEASONAL', $row);
        }

        // SKILL / COMPETITIVE BANDS
        $bands = [
            ['SKILL_PREMIER','Premier',10,10],
            ['SKILL_CHAMPIONSHIP','Championship',20,20],
            ['SKILL_FIRST','First Division',30,30],
            ['SKILL_SECOND','Second Division',40,40],
            ['SKILL_THIRD','Third Division',50,50],
            ['SKILL_SOCIAL','Social / Recreational',60,80],
            ['SKILL_DEVELOPMENT','Developmental',70,70],
        ];
        foreach ($bands as $row) {
            $append('RUG_SKILL_BAND', $row);
        }

        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id', 'code'],
            ['label', 'sort_order', 'numeric_rank', 'meta', 'updated_at']
        );
    }
}
