<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportClassificationGroup;
use App\Models\SportClassificationOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use JsonException;

class SportClassificationOptionController extends Controller
{
    public function index()
    {
        $options = SportClassificationOption::with(['group.sport'])
            ->ordered()
            ->get();

        return view('admin.sport-classification-options.index', compact('options'));
    }

    public function create()
    {
        $groups = SportClassificationGroup::with('sport')
            ->ordered()
            ->get();

        return view('admin.sport-classification-options.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        SportClassificationOption::create($data);

        return redirect()
            ->route('admin.sport_classification_options.index')
            ->with('success', 'Classification option created successfully.');
    }

    public function edit(SportClassificationOption $sport_classification_option)
    {
        $groups = SportClassificationGroup::with('sport')
            ->ordered()
            ->get();

        return view('admin.sport-classification-options.edit', [
            'option' => $sport_classification_option->load('group'),
            'groups' => $groups,
        ]);
    }

    public function update(Request $request, SportClassificationOption $sport_classification_option)
    {
        $data = $this->validatePayload($request, $sport_classification_option->id);

        $sport_classification_option->update($data);

        return redirect()
            ->route('admin.sport_classification_options.index')
            ->with('success', 'Classification option updated successfully.');
    }

    public function destroy(SportClassificationOption $sport_classification_option)
    {
        $sport_classification_option->delete();

        return redirect()
            ->route('admin.sport_classification_options.index')
            ->with('success', 'Classification option deleted.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $rule = Rule::unique('sport_classification_options', 'code')
            ->where(fn ($query) => $query->where('group_id', $request->input('group_id')));

        if ($ignoreId) {
            $rule->ignore($ignoreId);
        }

        $data = Validator::make($request->all(), [
            'group_id' => ['required', 'exists:sport_classification_groups,id'],
            'code' => ['required', 'string', 'max:64', $rule],
            'label' => ['required', 'string', 'max:191'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'numeric_rank' => ['nullable', 'integer', 'between:-2147483648,2147483647'],
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
