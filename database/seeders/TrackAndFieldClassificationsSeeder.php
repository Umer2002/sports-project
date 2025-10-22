<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrackAndFieldClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve sport_id (Track & Field / Athletics)
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['track and field','track & field','athletics'])
            ->orWhereIn('name', ['Track and Field','Track & Field','Athletics'])
            ->value('id');

        if (!$sportId) return;

        // 2) Groups (code => [name, order])
        $groups = [
            'TF_YOUTH_DEV'   => ['YOUTH / DEVELOPMENT PROGRAMS', 10],
            'TF_SCHOOL'      => ['SCHOOL / SCHOLASTIC', 20],
            'TF_COLLEGE'     => ['COLLEGE / UNIVERSITY (USA & CANADA)', 30],
            'TF_CLUB_ELITE'  => ['CLUB / OPEN / ELITE', 40],
            'TF_EVENT_DIV'   => ['EVENT SPECIALTIES (Competition Divisions)', 50],
            'TF_ADULT_MAST'  => ['ADULT & MASTERS (Age-Bracketed)', 60],
            'TF_PARA'        => ['PARA / ADAPTIVE DIVISIONS (World Para Athletics)', 70],
            'TF_MEET_CATS'   => ['TOURNAMENT / MEET CATEGORIES', 80],
            'TF_SEASONAL'    => ['SEASONAL / INTRAMURAL', 90],
            'TF_SKILL_BAND'  => ['SKILL / COMPETITIVE BANDS (used by some meets/clubs)', 100],
        ];

        // Upsert groups & collect IDs
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

        // YOUTH / DEVELOPMENT PROGRAMS
        $youth = [
            ['YTH_RJT','Run-Jump-Throw / Intro to Track',10,90, ['intro'=>true]],
            ['YTH_GRASSROOTS','Grassroots / Community Development',20,80],
            ['YTH_JUNIOR_DEV','Junior Development (local clubs)',30,70],
            ['YTH_PROV_STATE_DEV','Provincial / State Development Squads',40,50],
            ['YTH_HP_U16_U18','High-Performance U16 / U18',50,30, ['hp'=>true]],
        ];
        foreach ($youth as $row) {
            $append('TF_YOUTH_DEV', $row);
        }

        // SCHOOL / SCHOLASTIC
        $school = [
            ['SCH_ELEMENTARY','Elementary / Primary School',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HS_FRESH','High School: Freshman',30],
            ['SCH_HS_SOPH','High School: Sophomore',40],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',50],
            ['SCH_HS_VARS','High School: Varsity',60],
            ['SCH_PREP_IND','Prep / Independent School Programs',70],
        ];
        foreach ($school as $row) {
            $append('TF_SCHOOL', $row);
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
            $append('TF_COLLEGE', $row);
        }

        $collegeCA = [
            ['CA_USPORTS','Canada: U Sports (Men’s & Women’s Indoor/Outdoor)',60,25, ['country'=>'CA']],
            ['CA_CCAA','Canada: CCAA Colleges',70,45, ['country'=>'CA']],
            ['CA_UNI_CLUB','Canada: University Club / Intramural Track',80,65, ['country'=>'CA']],
        ];
        foreach ($collegeCA as $row) {
            $append('TF_COLLEGE', $row);
        }

        // CLUB / OPEN / ELITE
        $club = [
            ['CLUB_LOCAL','Local Track & Field Clubs',10,90],
            ['CLUB_REGIONAL_CHAMPS','Regional / District Championships',20,60],
            ['CLUB_STATE_PROV','State / Provincial Championships',30,50],
            ['CLUB_NAT_QUAL','National Qualifiers',40,35],
            ['CLUB_NAT_SENIOR','National Senior Championships',50,25],
            ['CLUB_DIAMOND','Diamond League / World Athletics Meets',60,10, ['global'=>true]],
            ['CLUB_OLY_TRIALS','Olympic Trials',70,12],
            ['CLUB_WORLDS_OLY','World Championships / Olympic Games',80,5, ['global'=>true]],
        ];
        foreach ($club as $row) {
            $append('TF_CLUB_ELITE', $row);
        }

        // EVENT SPECIALTIES
        $events = [
            ['EV_SPRINTS','Sprints (60 m, 100 m, 200 m, 400 m)',10, null, ['events'=>['60','100','200','400']]],
            ['EV_MIDDLE','Middle Distance (800 m, 1500 m, Mile)',20, null, ['events'=>['800','1500','Mile']]],
            ['EV_LONG','Long Distance (3k, 5k, 10k)',30, null, ['events'=>['3000','5000','10000']]],
            ['EV_HURDLES','Hurdles (60 mH, 100/110 mH, 400 mH)',40, null, ['events'=>['60H','100H','110H','400H']]],
            ['EV_RELAYS','Relays (4×100, 4×400, Medley)',50, null, ['events'=>['4x100','4x400','Medley']]],
            ['EV_JUMPS','Jumps (High, Long, Triple, Pole Vault)',60, null, ['events'=>['HJ','LJ','TJ','PV']]],
            ['EV_THROWS','Throws (Shot Put, Discus, Javelin, Hammer)',70, null, ['events'=>['SP','DT','JT','HT']]],
            ['EV_COMBINED','Combined Events (Pentathlon, Heptathlon, Decathlon)',80, null, ['events'=>['Pentathlon','Heptathlon','Decathlon']]],
            ['EV_RACEWALK','Race Walk',90],
            ['EV_STEEPLE','Steeplechase',100],
            ['EV_CROSS_COUNTRY','Cross-Country (distance by age group)',110, null, ['discipline'=>'XC']],
            ['EV_INDOOR_EQUIV','Indoor Track equivalents of above',120, null, ['discipline'=>'indoor']],
        ];
        foreach ($events as $row) {
            $append('TF_EVENT_DIV', $row);
        }

        // ADULT & MASTERS (Age-Bracketed)
        $adult = [
            ['AD_OPEN_19_34','Open / Senior (typically 19–34)',10,10, ['age'=>[19,34]]],
            ['AD_M35_39','Masters 35–39',20,20],
            ['AD_M40_44','Masters 40–44',30,30],
            ['AD_M45_49','Masters 45–49',40,40],
            ['AD_M50_54','Masters 50–54',50,50],
            ['AD_M55_59','Masters 55–59',60,60],
            ['AD_M60_64','Masters 60–64',70,70],
            ['AD_M65_69','Masters 65–69',80,80],
            ['AD_M70_74','Masters 70–74',90,90],
            ['AD_M75_79','Masters 75–79',100,100],
            ['AD_M80_PLUS','Masters 80+',110,110],
        ];
        foreach ($adult as $row) {
            $append('TF_ADULT_MAST', $row);
        }

        // PARA / ADAPTIVE DIVISIONS (World Para Athletics)
        $para = [
            ['PARA_T11_T13','T11–T13 (Vision Impairment)',10, null, ['class'=>'T11-T13']],
            ['PARA_T20','T20 (Intellectual Impairment)',20, null, ['class'=>'T20']],
            ['PARA_T31_T38','T31–T38 (Cerebral Palsy/Coordination)',30, null, ['class'=>'T31-T38']],
            ['PARA_T40_T47','T40–T47 (Limb Deficiency/Short Stature)',40, null, ['class'=>'T40-T47']],
            ['PARA_T51_T54','T51–T54 (Wheelchair Racing)',50, null, ['class'=>'T51-T54']],
            ['PARA_F40_F57','F40–F57 (Field Event Classes)',60, null, ['class'=>'F40-F57']],
        ];
        foreach ($para as $row) {
            $append('TF_PARA', $row);
        }

        // TOURNAMENT / MEET CATEGORIES
        $meets = [
            ['MEET_DEV_ALLCOMERS','Developmental / All-Comers',10,90],
            ['MEET_INVITES','Invitationals',20,70],
            ['MEET_LEAGUE','League Meets',30,65],
            ['MEET_CONFERENCE','Conference Championships',40,50],
            ['MEET_SECTIONAL','Sectionals / Regionals',50,45],
            ['MEET_NATIONALS','National Championships',60,20],
            ['MEET_INTL_GP','International Grand Prix',70,12],
            ['MEET_WORLD_IO','World Indoor / Outdoor',80,8, ['global'=>true]],
        ];
        foreach ($meets as $row) {
            $append('TF_MEET_CATS', $row);
        }

        // SEASONAL / INTRAMURAL
        $seasonal = [
            ['SEAS_INDOOR','Indoor Season (Winter)',10, null, ['season'=>'indoor']],
            ['SEAS_OUTDOOR','Outdoor Season (Spring/Summer)',20, null, ['season'=>'outdoor']],
            ['SEAS_XC','Cross-Country Season (Fall)',30, null, ['season'=>'xc']],
            ['SEAS_COL_INTRAM','College Intramural A / B / C',40],
        ];
        foreach ($seasonal as $row) {
            $append('TF_SEASONAL', $row);
        }

        // SKILL / COMPETITIVE BANDS
        $bands = [
            ['ELITE','Elite',10,10],
            ['PLATINUM','Platinum',20,20],
            ['GOLD','Gold',30,30],
            ['SILVER','Silver',40,40],
            ['BRONZE','Bronze',50,50],
            ['AAA','AAA',60,15],
            ['AA','AA',70,25],
            ['A','A',80,35],
            ['B','B',90,45],
            ['C','C',100,55],
        ];
        foreach ($bands as [$code,$label,$order,$rank]) {
            $payload[] = $opt('TF_SKILL_BAND','SKILL_'.$code,$label,$order,$rank,['band'=>$label]);
        }

        // 4) Upsert
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
