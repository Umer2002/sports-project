<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoxingClassificationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1) Resolve Boxing sport_id (your table shows "Boxing")
        $sportId = DB::table('sports')
            ->whereIn(DB::raw('LOWER(name)'), ['boxing'])
            ->orWhere('name', 'BOXING')
            ->value('id');

        if (!$sportId) return;

        // 2) Groups
        $groups = [
            'BOX_TRAIN_DEV'     => ['TRAINING & DEVELOPMENT STAGES', 10],
            'BOX_AM_LEVEL'      => ['AMATEUR COMPETITION LEVELS', 20],
            'BOX_PRO_TIER'      => ['PROFESSIONAL ORGANIZATION TIERS', 30],
            'BOX_WT_MEN_PRO'    => ['PROFESSIONAL MEN’S WEIGHT CLASSES (Unified Rules)', 40],
            'BOX_WT_WOMEN_PRO'  => ['PROFESSIONAL WOMEN’S WEIGHT CLASSES', 50],
            'BOX_SPECIAL'       => ['SPECIAL FORMATS & VARIANTS', 60],
            'BOX_SEASONAL'      => ['SEASONAL / INTRAMURAL', 70],
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
        $w = fn($min,$max,$note=null) => [
            'min_lbs'=>$min, 'max_lbs'=>$max,
            'min_kg'=>$min!==null? round($min/2.20462,1):null,
            'max_kg'=>$max!==null? round($max/2.20462,1):null,
            'note'=>$note
        ];

        $payload = [];

        // 3) TRAINING & DEVELOPMENT STAGES (lower rank = earlier stage)
        $train = [
            ['TR_NON_CONTACT','Non-Contact / Fitness Boxing',10,5],
            ['TR_BEGINNER','Beginner / Recreational',20,10],
            ['TR_NOVICE_AM','Novice Amateur (≤10 bouts)',30,20],
            ['TR_OPEN_AM','Open-Class Amateur (>10 bouts)',40,30],
            ['TR_ELITE_AM','Elite Amateur / National Team',50,40],
            ['TR_PRO','Professional',60,100],
        ];
        foreach ($train as [$c,$l,$o,$r]) $payload[] = $opt('BOX_TRAIN_DEV',$c,$l,$o,$r);

        // 4) AMATEUR COMPETITION LEVELS (approx ranks: local → world)
        $am = [
            ['AM_SMOKERS','Local/Club Shows (“Smokers”)',10,90],
            ['AM_REGIONAL','Regional Championships',20,70],
            ['AM_STATE_PROV','State / Provincial Championships',30,60],
            ['AM_GOLDEN_GLOVES','Golden Gloves',40,55],
            ['AM_NAT_GG_SG','National Golden Gloves / Silver Gloves (Youth)',50,50],
            ['AM_USA_CAN_NATS','USA Boxing Nationals / Boxing Canada Nationals',60,35],
            ['AM_PAN_AM','Pan-American Games',70,20],
            ['AM_OLY_TRIALS_OLY','Olympic Trials & Olympics',80,10],
            ['AM_WORLD_AIBA','World Amateur Championships (AIBA/IBA)',90,12],
        ];
        foreach ($am as [$c,$l,$o,$r]) $payload[] = $opt('BOX_AM_LEVEL',$c,$l,$o,$r);

        // 5) PROFESSIONAL ORGANIZATION TIERS
        $pro = [
            ['PRO_SMALL_HALL','Small-Hall / Local Pro Cards',10,80],
            ['PRO_REG_TITLES','Regional Titles (USBA, NABF, NABA, etc.)',20,60],
            ['PRO_NAT_TITLES','National Titles (USA / Canada)',30,45],
            ['PRO_WORLD_TITLES','International / World Titles (WBC, WBA, IBF, WBO, IBO)',40,10],
        ];
        foreach ($pro as [$c,$l,$o,$r]) $payload[] = $opt('BOX_PRO_TIER',$c,$l,$o,$r);

        // 6) PRO MEN — weight classes (numeric_rank: lighter = smaller number)
        $men = [
            ['MEN_MIN','Minimumweight / Strawweight: ≤105 lb (47.6 kg)',10,10, $w(null,105)],
            ['MEN_LF','Light Flyweight: >105–108 lb (49 kg)',20,20, $w(105,108)],
            ['MEN_FLY','Flyweight: >108–112 lb (50.8 kg)',30,30, $w(108,112)],
            ['MEN_SFLY','Super Flyweight: >112–115 lb (52.2 kg)',40,40, $w(112,115)],
            ['MEN_BANTAM','Bantamweight: >115–118 lb (53.5 kg)',50,50, $w(115,118)],
            ['MEN_SBANTAM','Super Bantamweight: >118–122 lb (55.3 kg)',60,60, $w(118,122)],
            ['MEN_FEATHER','Featherweight: >122–126 lb (57.2 kg)',70,70, $w(122,126)],
            ['MEN_SFEATHER','Super Featherweight: >126–130 lb (59 kg)',80,80, $w(126,130)],
            ['MEN_LIGHT','Lightweight: >130–135 lb (61.2 kg)',90,90, $w(130,135)],
            ['MEN_SLIGHT','Super Lightweight / Jr. Welter: >135–140 lb (63.5 kg)',100,100, $w(135,140)],
            ['MEN_WELTER','Welterweight: >140–147 lb (66.7 kg)',110,110, $w(140,147)],
            ['MEN_SWELTER','Super Welter / Jr. Middle: >147–154 lb (69.9 kg)',120,120, $w(147,154)],
            ['MEN_MIDDLE','Middleweight: >154–160 lb (72.6 kg)',130,130, $w(154,160)],
            ['MEN_SMIDDLE','Super Middle: >160–168 lb (76.2 kg)',140,140, $w(160,168)],
            ['MEN_LHW','Light Heavyweight: >168–175 lb (79.4 kg)',150,150, $w(168,175)],
            ['MEN_CRUISER','Cruiserweight: >175–200 lb (90.7 kg)',160,160, $w(175,200)],
            ['MEN_BRIDGER','Bridgerweight (new): >200–224 lb (101.6 kg)',170,170, $w(200,224,'new')],
            ['MEN_HEAVY','Heavyweight: >200 lb (no upper limit)',180,180, $w(200,null)],
        ];
        foreach ($men as [$c,$l,$o,$r,$meta]) $payload[] = $opt('BOX_WT_MEN_PRO',$c,$l,$o,$r,$meta + ['category'=>'men']);

        // 7) PRO WOMEN — weight classes (with Atomweight 102)
        $women = [
            ['WOM_ATOM','Atomweight: ≤102 lb (46.3 kg)',10,8, $w(null,102,'women-only')],
            ['WOM_MIN','Minimumweight / Strawweight: ≤105 lb (47.6 kg)',20,10, $w(null,105)],
            ['WOM_LF','Light Flyweight: >105–108 lb',30,20, $w(105,108)],
            ['WOM_FLY','Flyweight: >108–112 lb',40,30, $w(108,112)],
            ['WOM_SFLY','Super Flyweight: >112–115 lb',50,40, $w(112,115)],
            ['WOM_BANTAM','Bantamweight: >115–118 lb',60,50, $w(115,118)],
            ['WOM_SBANTAM','Super Bantamweight: >118–122 lb',70,60, $w(118,122)],
            ['WOM_FEATHER','Featherweight: >122–126 lb',80,70, $w(122,126)],
            ['WOM_SFEATHER','Super Featherweight: >126–130 lb',90,80, $w(126,130)],
            ['WOM_LIGHT','Lightweight: >130–135 lb',100,90, $w(130,135)],
            ['WOM_SLIGHT','Super Lightweight: >135–140 lb',110,100, $w(135,140)],
            ['WOM_WELTER','Welterweight: >140–147 lb',120,110, $w(140,147)],
            ['WOM_SWELTER','Super Welterweight: >147–154 lb',130,120, $w(147,154)],
            ['WOM_MIDDLE','Middleweight: >154–160 lb',140,130, $w(154,160)],
            ['WOM_SMIDDLE','Super Middleweight: >160–168 lb',150,140, $w(160,168)],
            ['WOM_LHW','Light Heavyweight: >168–175 lb',160,150, $w(168,175)],
            ['WOM_CRUISER','Cruiserweight: >175–200 lb',170,160, $w(175,200)],
            ['WOM_HEAVY','Heavyweight: >175 lb (no upper limit)',180,170, $w(175,null)],
        ];
        foreach ($women as [$c,$l,$o,$r,$meta]) $payload[] = $opt('BOX_WT_WOMEN_PRO',$c,$l,$o,$r,$meta + ['category'=>'women']);

        // 8) SPECIAL FORMATS & VARIANTS
        $special = [
            ['SP_WHITE_COLLAR','White-Collar Boxing (charity/fitness bouts)',10, null, ['variant'=>'white_collar']],
            ['SP_CORP_EXEC','Corporate / Executive Boxing',20],
            ['SP_MASTERS_AM','Masters Boxing (age-grouped amateur)',30, null, ['masters'=>true]],
            ['SP_KB_MUAY_XOVER','Kickboxing / Muay Thai cross-training events',40, null, ['cross_over'=>true]],
            ['SP_EXHIBITIONS','Exhibition Matches',50],
            ['SP_BKFC','Bare-Knuckle Boxing (BKFC and similar promotions)',60, null, ['bare_knuckle'=>true]],
        ];
        foreach ($special as $row) {
            [$c, $l, $o, $rank, $meta] = array_pad($row, 5, null);
            if ($meta === null && is_array($rank)) {
                $meta = $rank;
                $rank = null;
            }

            $payload[] = $opt('BOX_SPECIAL', $c, $l, $o, $rank, $meta);
        }

        // 9) SEASONAL / INTRAMURAL
        $seasonal = [
            ['SEAS_COLLEGE_CLUBS','College / University Boxing Clubs',10],
            ['SEAS_MILITARY','Military Boxing Programs',20],
            ['SEAS_COMM_INTRAM','Community Intramural Leagues',30],
        ];
        foreach ($seasonal as [$c,$l,$o]) $payload[] = $opt('BOX_SEASONAL',$c,$l,$o);

        // 10) Upsert
        DB::table('sport_classification_options')->upsert(
            $payload,
            ['group_id','code'],
            ['label','sort_order','numeric_rank','meta','updated_at']
        );
    }
}
