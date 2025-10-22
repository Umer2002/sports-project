<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MMAClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve MMA sport_id
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['mixed martial arts','mma'])
            ->orWhereIn('name', ['Mixed Martial Arts','MMA'])
            ->value('id');

        if (!$sportId) return;

        // 2) Groups (code => [label, sort])
        $groups = [
            'MMA_TRAIN_DEV'   => ['TRAINING & DEVELOPMENT STAGES', 10],
            'MMA_RULESET'     => ['RULESET / CONTACT LEVEL', 20],
            'MMA_WT_MEN'      => ['WEIGHT CLASSES – MEN (Unified Rules)', 30],
            'MMA_WT_WOMEN'    => ['WEIGHT CLASSES – WOMEN (Unified Rules)', 40],
            'MMA_AM_TIER'     => ['AMATEUR COMPETITION TIERS', 50],
            'MMA_PRO_TIER'    => ['PROFESSIONAL ORGANIZATION TIERS', 60],
            'MMA_SPECIAL'     => ['SPECIALTY FORMATS & VARIANTS', 70],
            'MMA_SKILL_BAND'  => ['SKILL / COMPETITIVE BANDS', 80],
            'MMA_SEASONAL'    => ['SEASONAL / INTRAMURAL', 90],
        ];

        // Upsert groups & fetch IDs
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

        // 3) TRAINING & DEVELOPMENT STAGES
        $train = [
            ['TR_BEGINNER','Beginner / Foundations',10,10],
            ['TR_NOVICE_REC','Novice / Recreational',20,20],
            ['TR_AMATEUR','Amateur (Sanctioned Amateur Bouts)',30,40],
            ['TR_ADV_AMATEUR','Advanced Amateur (title fights, 3-minute rounds)',40,50],
            ['TR_SEMI_PRO','Semi-Pro / “Class C” (some states)',50,60, ['regional'=>true]],
            ['TR_PRO','Professional',60,100],
        ];
        foreach ($train as $row) {
            $append('MMA_TRAIN_DEV', $row);
        }

        // 4) RULESET / CONTACT LEVEL
        $rules = [
            ['RS_NON_CONTACT','Non-Contact (drilling, fitness, shadow sparring)',10,10, ['contact'=>'none']],
            ['RS_LIGHT_SPARRING','Controlled Contact / Light Sparring',20,30, ['contact'=>'light']],
            ['RS_FULL_AM','Full Contact – Amateur',30,60, ['contact'=>'full','level'=>'amateur']],
            ['RS_FULL_PRO','Full Contact – Professional',40,100, ['contact'=>'full','level'=>'pro']],
        ];
        foreach ($rules as $row) {
            $append('MMA_RULESET', $row);
        }

        // 5) WEIGHT CLASSES – MEN
        // numeric_rank: smaller class = lower number
        $men = [
            ['MEN_STRAW', 'Strawweight: up to 115 lb (52.2 kg)',             10, 10,  0,   115],
            ['MEN_FLY',   'Flyweight: >115–125 lb (56.7 kg)',                20, 20, 115, 125],
            ['MEN_BANTAM','Bantamweight: >125–135 lb (61.2 kg)',             30, 30, 125, 135],
            ['MEN_FEATHER','Featherweight: >135–145 lb (65.8 kg)',           40, 40, 135, 145],
            ['MEN_LIGHT', 'Lightweight: >145–155 lb (70.3 kg)',              50, 50, 145, 155],
            ['MEN_SUPER_LIGHT','Super Lightweight (occasional): >155–165 lb',60, 55, 155, 165, 'occasional'],
            ['MEN_WELTER','Welterweight: >155–170 lb (77.1 kg)',             70, 60, 155, 170],
            ['MEN_SUPER_WELTER','Super Welterweight (occasional): >170–175', 80, 65, 170, 175, 'occasional'],
            ['MEN_MIDDLE','Middleweight: >170–185 lb (83.9 kg)',             90, 70, 170, 185],
            ['MEN_SUPER_MIDDLE','Super Middleweight (occasional): >185–195',100, 75, 185, 195, 'occasional'],
            ['MEN_LHW',   'Light Heavyweight: >185–205 lb (93.0 kg)',        110, 80, 185, 205],
            ['MEN_CRUISER','Cruiserweight (new): >205–225 lb (102.1 kg)',    120, 85, 205, 225, 'new'],
            ['MEN_HEAVY', 'Heavyweight: >205–265 lb (120.2 kg)',             130, 90, 205, 265],
            ['MEN_SUPER_HEAVY','Super Heavyweight: >265 lb (no upper limit)',140, 100,265, null],
        ];
        foreach ($men as $row) {
            [$code,$label,$order,$rank,$minLb,$maxLb,$note] = $row + [null,null,null,null,null,null,null];
            $payload[] = $opt('MMA_WT_MEN',$code,$label,$order,$rank,[
                'category' => 'men',
                'min_lbs'  => $minLb,
                'max_lbs'  => $maxLb,
                'min_kg'   => $minLb ? round($minLb/2.20462,1) : 0,
                'max_kg'   => $maxLb ? round($maxLb/2.20462,1) : null,
                'note'     => $note ?? null
            ]);
        }

        // 6) WEIGHT CLASSES – WOMEN
        $women = [
            ['WOM_ATOM','Atomweight: up to 105 lb (47.6 kg)',                 10, 10,  0,   105],
            ['WOM_STRAW','Strawweight: >105–115 lb (52.2 kg)',                20, 20, 105, 115],
            ['WOM_FLY','Flyweight: >115–125 lb (56.7 kg)',                    30, 30, 115, 125],
            ['WOM_BANTAM','Bantamweight: >125–135 lb (61.2 kg)',              40, 40, 125, 135],
            ['WOM_FEATHER','Featherweight: >135–145 lb (65.8 kg)',            50, 50, 135, 145],
            ['WOM_LIGHT','Lightweight (rare): >145–155 lb (70.3 kg)',         60, 60, 145, 155, 'rare'],
            ['WOM_WELTER','Welterweight (rare): >155–170 lb (77.1 kg)',       70, 70, 155, 170, 'rare'],
        ];
        foreach ($women as $row) {
            [$code,$label,$order,$rank,$minLb,$maxLb,$note] = $row + [null,null,null,null,null,null,null];
            $payload[] = $opt('MMA_WT_WOMEN',$code,$label,$order,$rank,[
                'category' => 'women',
                'min_lbs'  => $minLb,
                'max_lbs'  => $maxLb,
                'min_kg'   => $minLb ? round($minLb/2.20462,1) : 0,
                'max_kg'   => $maxLb ? round($maxLb/2.20462,1) : null,
                'note'     => $note ?? null
            ]);
        }

        // 7) AMATEUR COMPETITION TIERS
        $am = [
            ['AM_LOCAL','Local / Community Promotions',10,90],
            ['AM_REGIONAL','Regional Championships',20,70],
            ['AM_STATE_PROV','State / Provincial Championships',30,60],
            ['AM_NATIONAL','National Amateur Championships (IMMAF, USA MMA Federation, etc.)',40,30],
            ['AM_IMMAF_WORLD','International IMMAF World Championships',50,10],
        ];
        foreach ($am as $row) {
            $append('MMA_AM_TIER', $row);
        }

        // 8) PROFESSIONAL ORGANIZATION TIERS
        $pro = [
            ['PRO_REGIONAL','Regional Pro Circuits (e.g., King of the Cage, LFA, CFFC, TKO, BFL)',10,60],
            ['PRO_NATIONAL','National Promotions (e.g., Cage Fury, Titan FC, CES)',20,50],
            ['PRO_UFC','UFC',30,1, ['top_tier'=>true]],
            ['PRO_BELLATOR','Bellator MMA',40,2, ['top_tier'=>true]],
            ['PRO_PFL','Professional Fighters League (PFL)',50,3, ['top_tier'=>true]],
            ['PRO_ONE','ONE Championship (North American events)',60,4],
            ['PRO_INVICTA','Invicta FC (women’s)',70,10, ['women'=>true]],
            ['PRO_RIZIN','Rizin (international crossovers)',80,12],
        ];
        foreach ($pro as $row) {
            $append('MMA_PRO_TIER', $row);
        }

        // 9) SPECIAL FORMATS & VARIANTS
        $special = [
            ['SP_STRIKING_ONLY','Amateur “Striking Only” / Pancrase Rules',10, null, ['rules'=>'striking_only']],
            ['SP_GRAPPLING_ONLY','Grappling-Only Events (No-Gi, Combat Jiu-Jitsu)',20, null, ['rules'=>'grappling']],
            ['SP_HYBRID','Hybrid Rules (MMA/Kickboxing cross events)',30, null, ['rules'=>'hybrid']],
            ['SP_OPEN_CATCH','Openweight / Catchweight Bouts',40, null, ['weight'=>'open/catch']],
            ['SP_TEAM_MMA','Team MMA (rare exhibitions)',50, null, ['format'=>'team','rare'=>true]],
        ];
        foreach ($special as $row) {
            $append('MMA_SPECIAL', $row);
        }

        // 10) SKILL / COMPETITIVE BANDS
        $bands = [
            ['B_BEGINNER','Beginner',10,10],
            ['B_NOVICE','Novice',20,20],
            ['B_INTERMEDIATE','Intermediate',30,40],
            ['B_ADVANCED','Advanced',40,60],
            ['B_ELITE','Elite',50,80],
        ];
        foreach ($bands as [$c,$l,$o,$r]) $payload[] = $opt('MMA_SKILL_BAND',$c,$l,$o,$r,['band'=>$l]);

        // 11) SEASONAL / INTRAMURAL
        $seasonal = [
            ['SEAS_SMOKER','In-house Smoker Bouts',10],
            ['SEAS_INTER_GYM','Inter-Gym League Sparring',20],
            ['SEAS_COLLEGE_CLUBS','College MMA Clubs / Intramural Grappling Leagues',30],
        ];
        foreach ($seasonal as $row) {
            $append('MMA_SEASONAL', $row);
        }

        // 12) Upsert
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
