<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VolleyballClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Volleyball sport_id
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['volleyball'])
            ->orWhere('name', 'VOLLEYBALL')
            ->value('id');

        if (!$sportId) return;

        // 2) Groups (code => [label, sort_order])
        $groups = [
            'VB_YOUTH_DEV'   => ['YOUTH / DEVELOPMENT PATHWAY', 10],
            'VB_SCHOOL'      => ['SCHOOL / SCHOLASTIC', 20],
            'VB_CLUB_INDOOR' => ['CLUB & COMPETITIVE TIERS (Indoor)', 30],
            'VB_COLLEGE'     => ['COLLEGE / UNIVERSITY', 40],
            'VB_ADULT_REC'   => ['ADULT & RECREATIONAL (Indoor or Beach)', 50],
            'VB_BEACH'       => ['BEACH / SAND VOLLEYBALL', 60],
            'VB_PRO_ELITE'   => ['PROFESSIONAL / ELITE (Indoor)', 70],
            'VB_PARA'        => ['SITTING / PARA VOLLEYBALL', 80],
            'VB_FLIGHTS'     => ['TOURNAMENT / FLIGHT LABELS', 90],
            'VB_SEASONAL'    => ['SEASONAL & INTRAMURAL', 100],
        ];

        $groupIds = [];
        foreach ($groups as $code => [$name, $order]) {
            DB::table('sport_classification_groups')->updateOrInsert(
                ['sport_id' => $sportId, 'code' => $code],
                ['name' => $name, 'sort_order' => $order, 'created_at' => $now, 'updated_at' => $now]
            );
            $groupIds[$code] = DB::table('sport_classification_groups')
                ->where(['sport_id' => $sportId, 'code' => $code])->value('id');
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

        // 3) YOUTH / DEVELOPMENT PATHWAY
        $youth = [
            ['YD_MINI','Learn to Volley / Mini-Volley',10,10, ['intro'=>true]],
            ['YD_ATOMIC','Atomic / Smashball (Canada)',20,20, ['canada'=>true]],
            ['YD_HOUSE','Youth House / Community League',30,30],
            ['YD_DEV_REC_PLUS','Developmental / Rec+',40,40],
            ['YD_SELECT_TRAVEL','Select / Travel',50,50, ['travel'=>true]],
            ['YD_CLUB_U12_U18','Club Volleyball (U12–U18)',60,60, ['ages'=>['U12','U13','U14','U15','U16','U17','U18']]],
            ['YD_REGION_STATE','Regional / Provincial / State Teams',70,70],
            ['YD_HP_NAT_DEV','High-Performance / National Team Development Program',80,20, ['hp'=>true,'national_dev'=>true]],
        ];
        foreach ($youth as $row) {
            $append('VB_YOUTH_DEV', $row);
        }

        // 4) SCHOOL / SCHOLASTIC
        $school = [
            ['SCH_ELEM','Elementary / Primary',10],
            ['SCH_MIDDLE','Middle School / Junior High',20],
            ['SCH_HS_FRESH','High School: Freshman',30],
            ['SCH_HS_JV','High School: Junior Varsity (JV)',40],
            ['SCH_HS_VARS','High School: Varsity',50],
            ['SCH_PREP','Prep / Academy Programs',60],
        ];
        foreach ($school as $row) {
            $append('VB_SCHOOL', $row);
        }

        // 5) CLUB & COMPETITIVE TIERS (Indoor)
        $club = [
            ['CLUB_LOCAL_HOUSE','Local House League',10,90],
            ['CLUB_REG_A','Regional Club – A',20,70, ['tier'=>'A']],
            ['CLUB_REG_B','Regional Club – B',21,75, ['tier'=>'B']],
            ['CLUB_REG_C','Regional Club – C',22,80, ['tier'=>'C']],
            ['CLUB_NAT_POWER','National Club – Power League / Open',30,40, ['tier'=>'Power/Open']],
            ['CLUB_USAV_AGE','USA Volleyball Age Divisions (11U–18U)',40,50, ['ages'=>['11U','12U','13U','14U','15U','16U','17U','18U']]],
            ['CLUB_AAU','AAU Volleyball Divisions',50,55, ['org'=>'AAU']],
            ['CLUB_USAV_FLIGHTS','Open, National, USA, Liberty, Freedom, Patriot flights',60,45, ['flights'=>['Open','National','USA','Liberty','Freedom','Patriot']]],
            ['CLUB_VC_DIVS','Canadian National Championships: Div 1, 2, 3 (various age groups)',70,48, ['org'=>'Volleyball Canada','divs'=>['Div 1','Div 2','Div 3']]],
        ];
        foreach ($club as $row) {
            $append('VB_CLUB_INDOOR', $row);
        }

        // 6) COLLEGE / UNIVERSITY
        $college = [
            ['US_NJCAA','United States: NJCAA (Junior College)',10,60],
            ['US_NAIA','United States: NAIA',20,55],
            ['US_NCAA_M_D1_D2','United States: NCAA Men’s Div I/II',30,35, ['gender'=>'men']],
            ['US_NCAA_W_ALL','United States: NCAA Women’s Div I, II, III',40,30, ['gender'=>'women']],
            ['US_NCAA_W_BEACH','United States: NCAA Women’s Beach Volleyball',50,28, ['discipline'=>'beach','gender'=>'women']],
            ['CA_USPORTS','Canada: U Sports Men’s & Women’s Volleyball',60,32, ['country'=>'CA']],
            ['CA_CCAA','Canada: CCAA Colleges',70,45, ['country'=>'CA']],
            ['UNI_CLUB_INTRAM','University Club / Intramural Teams',80,70, ['club'=>true]],
        ];
        foreach ($college as $row) {
            $append('VB_COLLEGE', $row);
        }

        // 7) ADULT & RECREATIONAL (Indoor or Beach)
        $adult = [
            ['AR_REC_C','Recreational C',10,90],
            ['AR_REC_B','Recreational B',20,80],
            ['AR_INTERMEDIATE','Intermediate',30,60],
            ['AR_COMP_A','Competitive A',40,40],
            ['AR_COMP_AA','Competitive AA',50,30],
            ['AR_COMP_AAA_OPEN','Competitive AAA / Open',60,20],
            ['AR_CORP','Corporate / Workplace League',70,85, ['corporate'=>true]],
            ['AR_DRAFT_DROPIN','Draft / Drop-in Leagues',80,75],
            ['AR_MASTERS','Masters Age Divisions (25+, 30+, 35+, 40+, 45+, 50+)',90,65, ['masters'=>[25,30,35,40,45,50]]],
        ];
        foreach ($adult as $row) {
            $append('VB_ADULT_REC', $row);
        }

        // 8) BEACH / SAND VOLLEYBALL
        $beach = [
            ['B_YOUTH','Youth Sand Leagues (U12–U18)',10,70, ['discipline'=>'beach','ages'=>['U12','U14','U16','U18']]],
            ['B_COLLEGE','College Beach (NCAA Women’s, NAIA, NJCAA)',20,55, ['discipline'=>'beach']],
            ['B_AVP','AVP (Association of Volleyball Professionals)',30,30, ['discipline'=>'beach','pro'=>true]],
            ['B_AVP_NEXT','AVPNext / AVP America',40,40, ['discipline'=>'beach']],
            ['B_FIVB_PRO','FIVB Beach Pro Tour: Future, Challenge, Elite16',50,20, ['discipline'=>'beach','tiers'=>['Future','Challenge','Elite16']]],
            ['B_WORLD_OLY','World Championships / Olympics',60,10, ['discipline'=>'beach','global'=>true]],
            ['B_REC_FORMATS','Recreational 2-on-2, 3-on-3, 4-on-4',70, null, ['sizes'=>['2v2','3v3','4v4']]],
            ['B_COED','Coed Doubles / Quads',80, null, ['coed'=>true,'sizes'=>['2v2','4v4']]],
            ['B_GRASS_TRIPLES','Grass Triples',90, null, ['surface'=>'grass','size'=>'3v3']],
        ];
        foreach ($beach as $row) {
            $append('VB_BEACH', $row);
        }

        // 9) PROFESSIONAL / ELITE (Indoor)
        $pro = [
            ['PRO_USA_NT','USA Volleyball National Team (Men & Women)',10,20, ['country'=>'US','national'=>true]],
            ['PRO_CAN_NT','Volleyball Canada National Team',20,20, ['country'=>'CA','national'=>true]],
            ['PRO_VNL','FIVB Nations League',30,15, ['global'=>true]],
            ['PRO_PAN_AM','Pan Am Games',40,18],
            ['PRO_OLY','Olympic Games',50,10, ['global'=>true]],
            ['PRO_INTL_LEAGUES','International Pro Leagues (Italy SuperLega, Turkish, Brazilian, etc.)',60,12, ['intl_leagues'=>true]],
        ];
        foreach ($pro as $row) {
            $append('VB_PRO_ELITE', $row);
        }

        // 10) SITTING / PARA VOLLEYBALL
        $para = [
            ['PARA_SITTING','Sitting Volleyball – Men & Women',10, null, ['inclusive'=>true,'discipline'=>'sitting']],
            ['PARA_STANDING','Standing ParaVolley',20, null, ['inclusive'=>true]],
            ['PARA_PARALYMPIC','Paralympic Divisions',30, null, ['paralympic'=>true]],
        ];
        foreach ($para as $row) {
            $append('VB_PARA', $row);
        }

        // 11) TOURNAMENT / FLIGHT LABELS
        $flights = [
            ['FLT_BRONZE','Bronze',10,80],
            ['FLT_SILVER','Silver',20,60],
            ['FLT_GOLD','Gold',30,40],
            ['FLT_PLATINUM','Platinum',40,30],
            ['FLT_ELITE','Elite',50,20],
            ['FLT_OPEN','Open / Championship',60,15],
            ['FLT_FL1','Flight 1',70,90],
            ['FLT_FL2','Flight 2',80,92],
            ['FLT_FL3','Flight 3',90,94],
        ];
        foreach ($flights as $row) {
            $append('VB_FLIGHTS', $row);
        }

        // 12) SEASONAL & INTRAMURAL
        $seasonal = [
            ['SEAS_FALL_INDOOR','Fall Indoor League',10],
            ['SEAS_WINTER_INDOOR','Winter Indoor League',20],
            ['SEAS_SPR_SUM_BEACH','Spring/Summer Beach Season',30, null, ['discipline'=>'beach']],
            ['SEAS_COL_INTRAM','College Intramural A / B / C',40],
            ['SEAS_CORP_CHARITY','Corporate / Charity Tournaments',50, null, ['charity'=>true]],
        ];
        foreach ($seasonal as $row) {
            $append('VB_SEASONAL', $row);
        }

        // 13) Upsert
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
