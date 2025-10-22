<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Populate the legacy `divisions` table using the richer sport classification data.
     */
    public function run(): void
    {
        $divisionKeywords = ['division', 'tier', 'flight', 'level', 'bracket', 'league', 'class'];
        $codeKeywords = ['div', 'tier', 'lvl', 'flight', 'class'];

        $groups = DB::table('sport_classification_groups')
            ->select(['id', 'sport_id', 'name'])
            ->get();

        $sportIdsWithDivisions = [];

        foreach ($groups as $group) {
            $options = DB::table('sport_classification_options')
                ->select(['code', 'label', 'sort_order', 'numeric_rank'])
                ->where('group_id', $group->id)
                ->orderBy('sort_order')
                ->get();

            foreach ($options as $option) {
                $label = $option->label ?? '';
                $code = $option->code ?? '';

                $matchesLabel = $this->matchesKeywords($label, $divisionKeywords);
                $matchesCode = $this->matchesCodeKeywords($code, $codeKeywords);

                if (! $matchesLabel && ! $matchesCode) {
                    continue;
                }

                $sortOrder = $option->numeric_rank ?? $option->sort_order ?? 0;

                Division::updateOrCreate(
                    [
                        'sport_id' => $group->sport_id,
                        'name' => $label,
                    ],
                    [
                        'category' => $group->name,
                        'sort_order' => (int) $sortOrder,
                    ]
                );

                $sportIdsWithDivisions[$group->sport_id] = true;
            }
        }

        $existingSportIds = array_keys($sportIdsWithDivisions);

        DB::table('sports')
            ->select('id')
            ->when(! empty($existingSportIds), fn ($query) => $query->whereNotIn('id', $existingSportIds))
            ->get()
            ->each(function ($sport) {
                foreach ([
                    ['Premier Division', 10],
                    ['Competitive Division', 20],
                    ['Recreational Division', 30],
                ] as [$name, $order]) {
                    Division::updateOrCreate(
                        ['sport_id' => $sport->id, 'name' => $name],
                        ['category' => 'Competition Level', 'sort_order' => $order]
                    );
                }
            });
    }

    private function matchesKeywords(string $value, array $keywords): bool
    {
        if ($value === '') {
            return false;
        }

        $pattern = sprintf(
            '/\\b(%s)\\b/i',
            implode('|', array_map(fn ($keyword) => preg_quote($keyword, '/'), $keywords))
        );

        return (bool) preg_match($pattern, $value);
    }

    private function matchesCodeKeywords(string $value, array $keywords): bool
    {
        if ($value === '') {
            return false;
        }

        $pattern = sprintf(
            '/(^|_)(%s)(\\d+|_|$)/i',
            implode('|', array_map(fn ($keyword) => preg_quote($keyword, '/'), $keywords))
        );

        return (bool) preg_match($pattern, $value);
    }
}
