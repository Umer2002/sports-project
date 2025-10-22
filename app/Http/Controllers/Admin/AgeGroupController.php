<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AgeGroupController extends Controller
{
    public function index()
    {
        $ageGroups = AgeGroup::with('sport')->ordered()->get();

        return view('admin.age-groups.index', compact('ageGroups'));
    }

    public function create()
    {
        $sports = Sport::orderBy('name')->pluck('name', 'id');

        return view('admin.age-groups.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        AgeGroup::create($data);

        return redirect()
            ->route('admin.age_groups.index')
            ->with('success', 'Age group created successfully.');
    }

    public function edit(AgeGroup $age_group)
    {
        $sports = Sport::orderBy('name')->pluck('name', 'id');

        return view('admin.age-groups.edit', [
            'ageGroup' => $age_group,
            'sports' => $sports,
        ]);
    }

    public function update(Request $request, AgeGroup $age_group)
    {
        $data = $this->validatePayload($request, $age_group->id);

        $age_group->update($data);

        return redirect()
            ->route('admin.age_groups.index')
            ->with('success', 'Age group updated successfully.');
    }

    public function destroy(AgeGroup $age_group)
    {
        $age_group->delete();

        return redirect()
            ->route('admin.age_groups.index')
            ->with('success', 'Age group deleted.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $rule = Rule::unique('age_groups', 'code')
            ->where(fn ($query) => $query->where('sport_id', $request->input('sport_id')));

        if ($ignoreId) {
            $rule->ignore($ignoreId);
        }

        $validator = Validator::make($request->all(), [
            'sport_id' => ['required', 'exists:sports,id'],
            'code' => ['required', 'string', 'max:32', $rule],
            'label' => ['required', 'string', 'max:191'],
            'min_age_years' => ['nullable', 'integer', 'min:0', 'max:120'],
            'max_age_years' => ['nullable', 'integer', 'min:0', 'max:120'],
            'is_open_ended' => ['nullable', 'boolean'],
            'context' => ['nullable', 'string', 'max:191'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $min = $request->input('min_age_years');
            $max = $request->input('max_age_years');

            if ($min !== null && $max !== null && (int) $min > (int) $max) {
                $validator->errors()->add('max_age_years', 'The maximum age must be greater than or equal to the minimum age.');
            }
        });

        $data = $validator->validate();

        $data['is_open_ended'] = $request->boolean('is_open_ended');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
