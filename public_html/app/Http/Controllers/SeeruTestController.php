<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Flights\SeeruFlightsService;

class SeeruTestController extends Controller
{
    protected $flightService;

    public function __construct()
    {
        $this->flightService = new SeeruFlightsService();
    }

    public function testSearch(Request $request)
    {
        $params = [
            'trips' => $request->input('trips', 'oneway'),
            'adults' => $request->input('adults', 1),
            'children' => $request->input('children', 0),
            'infants' => $request->input('infants', 0),
            'origin' => $request->input('origin', 'DXB'),
            'destination' => $request->input('destination', 'LHR'),
            'departure_date' => $request->input('departure_date', now()->addDays(30)->format('Y-m-d')),
            'cabin_class' => $request->input('cabin_class', 'economy'),
        ];

        try {
            $results = $this->flightService->searchFlights($params);

            if ($results) {
                return response()->json([
                    'message' => 'Search successful!',
                    'search_id' => $results['search_id'] ?? null,
                    'params_used' => $params,
                ]);
            } else {
                return response()->json(['error' => 'Search failed.'], 500);
            }
        } catch (\Exception $e) {
            \Log::error('SeeruTestController Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Unexpected error during search.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
