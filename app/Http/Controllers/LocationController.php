<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function states(Request $request): JsonResponse
    {
        $countryId = (int) $request->query('country');

        if ($countryId < 1) {
            return response()->json(['data' => []]);
        }

        $rows = DB::table('states')
            ->where('country_id', $countryId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(fn ($row) => [
                'id' => (string) $row->id,
                'name' => $row->name,
            ]);

        return response()->json(['data' => $rows]);
    }

    public function cities(Request $request): JsonResponse
    {
        $stateId = (int) $request->query('state');

        if ($stateId < 1) {
            return response()->json(['data' => []]);
        }

        $rows = DB::table('cities')
            ->where('state_id', $stateId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(fn ($row) => [
                'id' => (string) $row->id,
                'name' => $row->name,
            ]);

        return response()->json(['data' => $rows]);
    }
}
