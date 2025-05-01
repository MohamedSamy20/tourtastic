<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Flights\SeeruFlightSearchService; // Keep this if testSearch is still used
use App\Services\Flights\SeeruAuthService; // Import the auth service
use Illuminate\Support\Facades\Log;

class SeeruTestController extends Controller
{
    protected $searchService;
    protected $authService; // Add property for auth service

    // Inject both services via the constructor
    public function __construct(SeeruFlightSearchService $searchService, SeeruAuthService $authService)
    {
        $this->searchService = $searchService;
        $this->authService = $authService; // Assign injected auth service
    }

    /**
     * Test flight search functionality.
     * (Keep your existing testSearch method here)
     */
    public function testSearch(Request $request)
    {
        // ... (existing code from SeeruTestController_dependency_injection_fix.php) ...
        $params = [
            // Use parameters from the request or define defaults
            'trips' => $request->input('trips', 'oneway'),
            'adults' => $request->input('adults', 1),
            'children' => $request->input('children', 0),
            'infants' => $request->input('infants', 0),
            'origin' => $request->input('origin', 'DXB'), // Example: Dubai
            'destination' => $request->input('destination', 'LHR'), // Example: London Heathrow
            'departure_date' => $request->input('departure_date', now()->addDays(30)->format('Y-m-d')), // Example: 30 days from now
            'cabin_class' => $request->input('cabin_class', 'economy'),
        ];

        try {
            $results = $this->searchService->searchFlights($params);

            if ($results) {
                return response()->json([
                    'message' => 'Search successful!',
                    'search_id' => $results['search_id'] ?? null,
                    'params_used' => $params,
                ]);
            } else {
                Log::warning('SeeruTestController: searchFlights service returned null or empty.', ['params' => $params]);
                return response()->json(['error' => 'Search request failed at the service level. Check logs.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('SeeruTestController Exception: ' . $e->getMessage(), [
                'params' => $params,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An unexpected error occurred during the search.', 'exception_message' => $e->getMessage()], 500);
        }
    }

    /**
     * NEW METHOD: Test Seeru Authentication directly.
     */
    public function testAuth(Request $request)
    {
        Log::info('SeeruTestController: testAuth endpoint hit.');
        try {
            // Force refresh to bypass cache during testing
            $token = $this->authService->getAccessToken(true);

            if ($token) {
                Log::info('SeeruTestController: testAuth successful. Token obtained.');
                // Return only a portion of the token for security in response, log the full one if needed
                return response()->json([
                    'message' => 'Authentication successful!',
                    'token_obtained' => true,
                    'token_preview' => substr($token, 0, 10) . '...' . substr($token, -10) // Show only beginning and end
                ]);
            } else {
                Log::error('SeeruTestController: testAuth failed. getAccessToken returned null.');
                return response()->json([
                    'message' => 'Authentication failed. Token could not be obtained. Check logs.',
                    'token_obtained' => false,
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('SeeruTestController testAuth Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'An unexpected error occurred during authentication test.',
                'exception_message' => $e->getMessage(),
                'token_obtained' => false,
            ], 500);
        }
    }
}

