<?php
namespace Modules\Flight\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\services\Flights\SeeruFlightSearchService; // Old direct service usage
// use App\services\Flights\SeeruBookingService; // Old direct service usage
use App\Services\Flights\FlightService; // Use the wrapper service
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SeeruFlightController extends Controller // Consider renaming or merging if this becomes the main FlightController
{
    protected $flightService;

    // Inject the FlightService wrapper
    public function __construct(FlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    /**
     * Handle flight search requests using the active flight provider.
     */
    public function search(Request $request)
    {
        if (!$this->flightService->hasActiveProvider()) {
            return redirect()->back()->with("error", "Flight search is currently unavailable. Please contact support.");
        }

        // Build API parameters (Keep this logic or adapt if needed)
        $params = $this->buildSearchParams($request);

        // Make API call via the wrapper service
        $searchResults = $this->flightService->searchFlights($params);
        
        if (!$searchResults || empty($searchResults["search_id"])) {
            Log::error("Flight API Error: Failed to get search results", ["provider" => get_class($this->flightService->getActiveProviderService()), "params" => $params, "response" => $searchResults]);
            return redirect()->back()->with("error", "Flight search failed. Please try again later.");
        }

        // Get detailed results using search_id via the wrapper service
        $detailedResults = $this->flightService->getSearchResult($searchResults["search_id"]);
        
        if (!$detailedResults || empty($detailedResults["flights"])) { // Adjust key based on actual API response structure
            Log::error("Flight API Error: Failed to get detailed flight results", ["provider" => get_class($this->flightService->getActiveProviderService()), "search_id" => $searchResults["search_id"], "response" => $detailedResults]);
            // Don't necessarily fail here, maybe the search just had no results. Check API specific error codes/messages if available.
            // For now, proceed but log the issue.
            // return redirect()->back()->with("error", "Could not retrieve flight details. Please try again later.");
        }

        // Process the API response (Keep this logic or adapt based on API structure)
        $flights = $this->processApiResponse($detailedResults);

        // Store search_id in session for later use
        session(["flight_search_id" => $searchResults["search_id"]]); // Use a generic session key

        // Prepare data for view
        $data = [
            "rows" => $flights,
            "search_params" => $request->all(),
            "seo_meta" => [
                "title" => "Flight Search Results",
                "desc" => "Find the best flight deals"
            ]
        ];

        // Ensure the view path is correct for the MyTravel script structure
        return view("Flight::frontend.search", $data);
    }

    /**
     * Build search parameters for the flight API.
     * This might need adjustments based on the specific active provider.
     * Consider moving provider-specific logic into the service classes.
     */
    private function buildSearchParams(Request $request): array
    {
        // Basic parameters - common across providers?
        $params = [
            "trips" => "oneway", // Default, might be overridden
            "adults" => $request->input("adults", 1),
            "children" => $request->input("children", 0),
            "infants" => $request->input("infants", 0),
            "cabin_class" => $request->input("seat_type", "economy"), // Map frontend key to API key
            "direct_flights" => $request->boolean("direct_flights", false) // Assuming a checkbox or similar
        ];

        // Origin and Destination
        if ($request->filled("from_where")) {
            // Assuming IATA code is expected
            $params["origin"] = $request->input("from_where"); 
        }
        if ($request->filled("to_where")) {
            // Assuming IATA code is expected
            $params["destination"] = $request->input("to_where");
        }

        // Date handling
        if ($request->filled("date")) {
            $dates = explode(" - ", $request->input("date"));
            $params["departure_date"] = Carbon::parse($dates[0])->format("Y-m-d");
            
            // Check if it's a round trip based on date range picker
            if (count($dates) > 1) {
                $params["trips"] = "return";
                $params["return_date"] = Carbon::parse($dates[1])->format("Y-m-d");
            }
        }
        
        // Add trip type explicitly if provided (e.g., from radio buttons)
        if ($request->filled("trip_type")) {
             $params["trips"] = $request->input("trip_type"); // e.g., 'oneway', 'return', 'multicity'
             if ($params["trips"] !== 'return' && isset($params["return_date"])) {
                 unset($params["return_date"]); // Remove return date if not a round trip
             }
        }

        // TODO: Add multi-city parameters if supported

        return $params;
    }

    /**
     * Process flight API response into a standardized format for the view.
     * This might need adjustments based on the specific active provider.
     * Consider moving provider-specific logic into the service classes.
     */
    private function processApiResponse(?array $apiData): \Illuminate\Support\Collection
    {
        $flights = collect();

        if (empty($apiData) || empty($apiData["flights"])) { // Adjust key based on actual API response
            return $flights;
        }

        // Assuming Seeru structure for now - needs generalization or provider-specific mapping
        foreach ($apiData["flights"] as $flight) {
            if (empty($flight["segments"]) || empty($flight["segments"][0])) continue; // Skip if segments are missing
            
            $outboundSegments = $flight["segments"][0]; // Assuming first segment array is outbound
            $firstOutbound = $outboundSegments[0];
            $lastOutbound = $outboundSegments[count($outboundSegments) - 1];

            // Inbound segments (if available)
            $inboundSegments = $flight["segments"][1] ?? null;
            $firstInbound = $inboundSegments ? $inboundSegments[0] : null;
            $lastInbound = $inboundSegments ? $inboundSegments[count($inboundSegments) - 1] : null;

            $flightObj = new \stdClass();
            $flightObj->id = $flight["flight_id"]; // Provider-specific flight identifier
            $flightObj->price = $flight["price"]["total"] ?? null;
            $flightObj->currency = $flight["price"]["currency"] ?? null;
            $flightObj->stops = count($outboundSegments) - 1; // Simple stop count for outbound
            
            // Outbound Details
            $flightObj->origin = $firstOutbound["departure_airport"];
            $flightObj->destination = $lastOutbound["arrival_airport"];
            $flightObj->departure_time = Carbon::parse($firstOutbound["departure_time"]);
            $flightObj->arrival_time = Carbon::parse($lastOutbound["arrival_time"]);
            $flightObj->duration = $this->calculateDuration($firstOutbound["departure_time"], $lastOutbound["arrival_time"]); // Helper needed

            // Airline (use first segment's airline)
            $airlineObj = new \stdClass();
            $airlineObj->name = $firstOutbound["airline_name"];
            $airlineObj->code = $firstOutbound["airline_code"];
            // $airlineObj->image_url = ... // Get from config or DB based on code?
            $flightObj->airline = $airlineObj;
            
            // Airports (use first/last segment's airports)
            $airportFromObj = new \stdClass();
            $airportFromObj->name = $firstOutbound["departure_airport_name"];
            $airportFromObj->code = $firstOutbound["departure_airport"];
            $flightObj->airportFrom = $airportFromObj;
            
            $airportToObj = new \stdClass();
            $airportToObj->name = $lastOutbound["arrival_airport_name"];
            $airportToObj->code = $lastOutbound["arrival_airport"];
            $flightObj->airportTo = $airportToObj;

            // Add Inbound Details if present
            if ($lastInbound) {
                 $flightObj->return_departure_time = Carbon::parse($firstInbound["departure_time"]);
                 $flightObj->return_arrival_time = Carbon::parse($lastInbound["arrival_time"]);
                 $flightObj->return_duration = $this->calculateDuration($firstInbound["departure_time"], $lastInbound["arrival_time"]);
                 $flightObj->return_stops = count($inboundSegments) - 1;
            }
            
            // Add raw segment data if needed by the view for details display
            $flightObj->segments = $flight["segments"]; 

            $flights->push($flightObj);
        }

        return $flights;
    }
    
    /**
     * Calculate duration between two datetime strings.
     */
    private function calculateDuration($start, $end): string
    {
        try {
            $startTime = Carbon::parse($start);
            $endTime = Carbon::parse($end);
            return $startTime->diff($endTime)->format('%Hh %Im');
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Handle flight booking requests using the active flight provider.
     */
    public function book(Request $request)
        // Clear session data
        session()->forget('seeru_search_id');
        session()->forget('seeru_booking_data');
        
        // Redirect to booking confirmation page
        return redirect()->route('flight.booking.confirmation', ['booking_id' => $bookingResult['booking_id']]);
    }

    /**
     * Display booking confirmation
     */
    public function bookingConfirmation($bookingId)
    {
        // Here you would typically fetch booking details from your database
        // For this example, we'll just display the booking ID
        
        return view('Flight::frontend.booking-confirmation', [
            'booking_id' => $bookingId
        ]);
    }
}

        
        // Clear session data
        session()->forget('seeru_search_id');
        session()->forget('seeru_booking_data');
        
        // Redirect to booking confirmation page
        return redirect()->route('flight.booking.confirmation', ['booking_id' => $bookingResult['booking_id']]);
    }

    /**
     * Display booking confirmation
     */
    public function bookingConfirmation($bookingId)
    {
        // Here you would typically fetch booking details from your database
        // For this example, we'll just display the booking ID
        
        return view('Flight::frontend.booking-confirmation', [
            'booking_id' => $bookingId
        ]);
    }
}
