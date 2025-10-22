<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\SportClassificationGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use JsonException;

class SportClassificationGroupController extends Controller
{
    public function index()
    {
        $groups = SportClassificationGroup::with('sport')
            ->withCount('options')
            ->ordered()
            ->get();

        return view('admin.sport-classification-groups.index', compact('groups'));
    }

    public function create()
    {
        $sports = Sport::orderBy('name')->pluck('name', 'id');

        return view('admin.sport-classification-groups.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        SportClassificationGroup::create($data);

        return redirect()
            ->route('admin.sport_classification_groups.index')
            ->with('success', 'Classification group created successfully.');
    }

    public function edit(SportClassificationGroup $sport_classification_group)
    {
        $sports = Sport::orderBy('name')->pluck('name', 'id');

        return view('admin.sport-classification-groups.edit', [
            'group' => $sport_classification_group,
            'sports' => $sports,
        ]);
    }

    public function update(Request $request, SportClassificationGroup $sport_classification_group)
    {
        $data = $this->validatePayload($request, $sport_classification_group->id);

        $sport_classification_group->update($data);

        return redirect()
            ->route('admin.sport_classification_groups.index')
            ->with('success', 'Classification group updated successfully.');
    }

    public function destroy(SportClassificationGroup $sport_classification_group)
    {
        $sport_classification_group->delete();

        return redirect()
            ->route('admin.sport_classification_groups.index')
            ->with('success', 'Classification group deleted.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $rule = Rule::unique('sport_classification_groups', 'code')
            ->where(fn ($query) => $query->where('sport_id', $request->input('sport_id')));

        if ($ignoreId) {
            $rule->ignore($ignoreId);
        }

        $data = Validator::make($request->all(), [
            'sport_id' => ['required', 'exists:sports,id'],
            'code' => ['required', 'string', 'max:64', $rule],
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:512'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'meta' => ['nullable', 'string'],
        ])->validate();

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['meta'] = $this->transformMeta($request->input('meta'));

        return $data;
    }

    private function transformMeta(?string $meta): ?array
    {
        if ($meta === null || trim($meta) === '') {
            return null;
        }

        try {
            $decoded = json_decode($meta, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw ValidationException::withMessages([
                'meta' => 'Meta must be valid JSON.',
            ]);
        }

        if (!is_array($decoded)) {
            throw ValidationException::withMessages([
                'meta' => 'Meta JSON must decode to an object or array.',
            ]);
        }

        return $decoded;
    }
}
