<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\State;
use App\Models\Venue;
use App\Models\VenueAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::with(['country', 'state', 'city'])->get();
        return view('admin.venues.index', compact('venues'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return view('admin.venues.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('venues', 'name')],
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'type' => 'nullable|string|max:255',
            'country_id' => ['required', 'integer', Rule::exists('countries', 'id')],
            'state_id' => [
                'required',
                'integer',
                Rule::exists('states', 'id')->where(fn ($query) => $query->where('country_id', $request->integer('country_id'))),
            ],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(fn ($query) => $query->where('state_id', $request->integer('state_id'))),
            ],
        ]);

        if (blank($data['location'])) {
            $data['location'] = $this->buildLocationLabel(
                $data['city_id'] ?? null,
                $data['state_id'] ?? null,
                $data['country_id'] ?? null
            );
        }

        $venue = Venue::create($data);

        // Use Google Places API to find nearby hotels
        $googleApiKey = env('GOOGLE_PLACES_API_KEY');
        $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
            'location' => $this->getCoordinates($venue->location),
            'radius' => 3000,
            'type' => 'lodging',
            'key' => $googleApiKey
        ]);

        if ($response->successful()) {
            $hotels = $response->json('results');
            foreach ($hotels as $hotel) {
                Hotel::create([
                    'tournament_id' => null,
                    'venue_id' => $venue->id,
                    'name' => $hotel['name'] ?? 'Unknown',
                    'address' => $hotel['vicinity'] ?? 'N/A',
                    'google_place_id' => $hotel['place_id'] ?? null,
                ]);
            }
        }

        return redirect()->route('admin.venues.index')->with('success', 'Venue and nearby hotels saved successfully.');
    }

    private function getCoordinates($location)
    {
        $apiKey = env('GOOGLE_GEOCODING_API_KEY', env('GOOGLE_PLACES_API_KEY'));
        $geoResponse = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $location,
            'key' => $apiKey
        ]);

        if ($geoResponse->successful() && !empty($geoResponse['results'][0]['geometry']['location'])) {
            $loc = $geoResponse['results'][0]['geometry']['location'];
            return "{$loc['lat']},{$loc['lng']}";
        }

        return '31.5497,74.3436'; // fallback to Lahore
    }

    public function show(Venue $venue)
    {
        $venue->load(['availabilities', 'hotels', 'country', 'state', 'city']);
        return view('admin.venues.show', compact('venue'));
    }

    public function edit(Venue $venue)
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);
        $states = collect();
        $cities = collect();

        if ($venue->country_id) {
            $states = State::where('country_id', $venue->country_id)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        if ($venue->state_id) {
            $cities = City::where('state_id', $venue->state_id)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('admin.venues.edit', compact('venue', 'countries', 'states', 'cities'));
    }

    public function update(Request $request, Venue $venue)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('venues', 'name')->ignore($venue->id)],
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'type' => 'nullable|string|max:255',
            'country_id' => ['required', 'integer', Rule::exists('countries', 'id')],
            'state_id' => [
                'required',
                'integer',
                Rule::exists('states', 'id')->where(fn ($query) => $query->where('country_id', $request->integer('country_id'))),
            ],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(fn ($query) => $query->where('state_id', $request->integer('state_id'))),
            ],
        ]);

        if (blank($data['location'])) {
            $data['location'] = $this->buildLocationLabel(
                $data['city_id'] ?? null,
                $data['state_id'] ?? null,
                $data['country_id'] ?? null
            );
        }

        $venue->update($data);
        return redirect()->route('admin.venues.index')->with('success', 'Venue updated successfully.');
    }

    public function destroy(Venue $venue)
    {
        $venue->delete();
        return redirect()->route('admin.venues.index')->with('success', 'Venue deleted.');
    }

    public function storeAvailability(Request $request, Venue $venue)
    {
        $request->validate([
            'available_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $venue->availabilities()->create($request->only(['available_date', 'start_time', 'end_time']));
        return back()->with('success', 'Availability added!');
    }

    public function states(Request $request)
    {
        $countryId = $request->integer('country_id');

        if (!$countryId) {
            return response()->json(['data' => []]);
        }

        $states = State::where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $states]);
    }

    public function cities(Request $request)
    {
        $stateId = $request->integer('state_id');

        if (!$stateId) {
            return response()->json(['data' => []]);
        }

        $cities = City::where('state_id', $stateId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $cities]);
    }

    private function buildLocationLabel(?int $cityId, ?int $stateId, ?int $countryId): string
    {
        $parts = [];

        if ($cityId) {
            $city = City::find($cityId);
            if ($city) {
                $parts[] = $city->name;
            }
        }

        if ($stateId) {
            $state = State::find($stateId);
            if ($state) {
                $parts[] = $state->name;
            }
        }

        if ($countryId) {
            $country = Country::find($countryId);
            if ($country) {
                $parts[] = $country->name;
            }
        }

        return implode(', ', $parts);
    }
}
