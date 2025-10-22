<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationLookupController extends Controller
{
    public function states(Request $request): JsonResponse
    {
        $countryId = $request->query('country');

        if (! $countryId) {
            return response()->json(['data' => []]);
        }

        $states = State::query()
            ->where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id', 'name', 'country_id'])
            ->map(fn (State $state) => [
                'id' => (string) $state->id,
                'name' => $state->name,
            ]);

        return response()->json(['data' => $states]);
    }

    public function cities(Request $request): JsonResponse
    {
        $stateId = $request->query('state');

        if (! $stateId) {
            return response()->json(['data' => []]);
        }

        $cities = City::query()
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get(['id', 'name', 'state_id'])
            ->map(fn (City $city) => [
                'id' => (string) $city->id,
                'name' => $city->name,
            ]);

        return response()->json(['data' => $cities]);
    }
}
