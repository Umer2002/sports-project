<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GenderController extends Controller
{
    public function index()
    {
        $genders = Gender::with('sport')->ordered()->get();

        return view('admin.genders.index', compact('genders'));
    }

    public function create()
    {
        $sports = Sport::orderBy('name')->pluck('name', 'id');

        return view('admin.genders.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        Gender::create($data);

        return redirect()
            ->route('admin.genders.index')
            ->with('success', 'Gender created successfully.');
    }

    public function edit(Gender $gender)
    {
        $sports = Sport::orderBy('name')->pluck('name', 'id');

        return view('admin.genders.edit', compact('gender', 'sports'));
    }

    public function update(Request $request, Gender $gender)
    {
        $data = $this->validatePayload($request, $gender->id);

        $gender->update($data);

        return redirect()
            ->route('admin.genders.index')
            ->with('success', 'Gender updated successfully.');
    }

    public function destroy(Gender $gender)
    {
        $gender->delete();

        return redirect()
            ->route('admin.genders.index')
            ->with('success', 'Gender deleted.');
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $rule = Rule::unique('genders', 'code')
            ->where(fn ($query) => $query->where('sport_id', $request->input('sport_id')));

        if ($ignoreId) {
            $rule->ignore($ignoreId);
        }

        $data = Validator::make($request->all(), [
            'sport_id' => ['required', 'exists:sports,id'],
            'code' => ['required', 'string', 'max:32', $rule],
            'label' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ])->validate();

        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
