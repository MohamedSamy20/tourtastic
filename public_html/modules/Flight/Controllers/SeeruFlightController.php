<?php
namespace Modules\Flight\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\services\Flights\SeeruFlightSearchService;
use App\services\Flights\SeeruBookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SeeruFlightController extends Controller
{
    protected $searchService;
    protected $bookingService;

    public function __construct()
    {
        $this->searchService = new SeeruFlightSearchService();
        $this->bookingService = new SeeruBookingService();
    }

    /**
     * Handle flight search requests with Seeru API
     */
    public function search(Request $request)
    {
        // Build API parameters
        $params = $this->buildSearchParams($request);

        // Make API call
        $searchResults = $this->searchService->searchFlights($params);
        
        if (!$searchResults || empty($searchResults['search_id'])) {
            Log::error('Seeru API Error: Failed to get search results');
            return redirect()->back()->with('error', 'Flight search failed. Please try again.');
        }

        // Get detailed results using search_id
        $detailedResults = $this->searchService->getSearchResult($searchResults['search_id']);
        
        if (!$detailedResults || empty($detailedResults['flights'])) {
            Log::error('Seeru API Error: Failed to get detailed flight results');
            return redirect()->back()->with('error', 'Flight search failed. Please try again.');
        }

        $flights = $this->processApiResponse($detailedResults);

        // Store search_id in session for later use
        session(['seeru_search_id' => $searchResults['search_id']]);

        // Prepare data for view
        $data = [
            'rows' => $flights,
            'search_params' => $request->all(),
            'seo_meta' => [
                'title' => 'Flight Search Results',
                'desc' => 'Find the best flight deals'
            ]
        ];

        return view('Flight::frontend.search', $data);
    }

    /**
     * Build search parameters for Seeru API
     */
    private function buildSearchParams(Request $request)
    {
        $params = [
            'trips' => 'oneway',
            'adults' => $request->input('adults', 1),
            'children' => $request->input('children', 0),
            'infants' => $request->input('infants', 0),
            'cabin_class' => $request->input('seat_type', 'economy'),
            'direct_flights' => false
        ];

        // Origin and Destination
        if ($request->filled('from_where')) {
            $params['origin'] = $request->input('from_where');
        }
        if ($request->filled('to_where')) {
            $params['destination'] = $request->input('to_where');
        }

        // Date handling
        if ($request->filled('date')) {
            $dates = explode(' - ', $request->input('date'));
            $params['departure_date'] = Carbon::parse($dates[0])->format('Y-m-d');
            
            if (count($dates) > 1 && $params['trips'] == 'oneway') {
                $params['trips'] = 'return';
                $params['return_date'] = Carbon::parse($dates[1])->format('Y-m-d');
            }
        }

        return $params;
    }

    /**
     * Process Seeru API response
     */
    private function processApiResponse($apiData)
    {
        $flights = collect();

        if (!empty($apiData['flights'])) {
            foreach ($apiData['flights'] as $flight) {
                $segments = $flight['segments'][0]; // Get first segment for oneway or outbound
                
                $flightObj = new \stdClass();
                $flightObj->id = $flight['flight_id'];
                $flightObj->origin = $segments[0]['departure_airport'];
                $flightObj->destination = $segments[count($segments) - 1]['arrival_airport'];
                $flightObj->departure_time = Carbon::parse($segments[0]['departure_time']);
                $flightObj->arrival_time = Carbon::parse($segments[count($segments) - 1]['arrival_time']);
                $flightObj->price = $flight['price']['total'];
                $flightObj->currency = $flight['price']['currency'];
                
                // Create airline object
                $airlineObj = new \stdClass();
                $airlineObj->name = $segments[0]['airline_name'];
                $airlineObj->code = $segments[0]['airline_code'];
                $flightObj->airline = $airlineObj;
                
                // Create airport objects
                $airportFromObj = new \stdClass();
                $airportFromObj->name = $segments[0]['departure_airport_name'];
                $airportFromObj->code = $segments[0]['departure_airport'];
                $flightObj->airportFrom = $airportFromObj;
                
                $airportToObj = new \stdClass();
                $airportToObj->name = $segments[count($segments) - 1]['arrival_airport_name'];
                $airportToObj->code = $segments[count($segments) - 1]['arrival_airport'];
                $flightObj->airportTo = $airportToObj;
                
                $flights->push($flightObj);
            }
        }

        return $flights;
    }

    /**
     * Handle flight booking
     */
    public function booking(Request $request)
    {
        $searchId = session('seeru_search_id');
        $flightId = $request->input('flight_id');
        
        if (!$searchId || !$flightId) {
            return redirect()->back()->with('error', 'Invalid booking request');
        }
        
        // Check fare validity
        $fareResult = $this->bookingService->checkFare([
            'search_id' => $searchId,
            'flight_id' => $flightId
        ]);
        
        if (!$fareResult || !isset($fareResult['valid']) || !$fareResult['valid']) {
            return redirect()->back()->with('error', 'Selected fare is no longer available');
        }
        
        // Store booking data in session for checkout
        session([
            'seeru_booking_data' => [
                'search_id' => $searchId,
                'flight_id' => $flightId,
                'fare_data' => $fareResult
            ]
        ]);
        
        // Redirect to passenger information page
        return redirect()->route('flight.checkout');
    }

    /**
     * Handle flight checkout
     */
    public function checkout(Request $request)
    {
        $bookingData = session('seeru_booking_data');
        
        if (!$bookingData) {
            return redirect()->route('flight.search')->with('error', 'No booking in progress');
        }
        
        // Display checkout form
        return view('Flight::frontend.checkout', [
            'booking_data' => $bookingData
        ]);
    }

    /**
     * Complete booking
     */
    public function completeBooking(Request $request)
    {
        $bookingData = session('seeru_booking_data');
        
        if (!$bookingData) {
            return redirect()->route('flight.search')->with('error', 'No booking in progress');
        }
        
        // Validate passenger information
        $request->validate([
            'passengers.*.type' => 'required',
            'passengers.*.title' => 'required',
            'passengers.*.first_name' => 'required',
            'passengers.*.last_name' => 'required',
            'passengers.*.gender' => 'required',
            'passengers.*.date_of_birth' => 'required|date',
            'contact.email' => 'required|email',
            'contact.phone' => 'required',
        ]);
        
        // Prepare booking parameters
        $bookingParams = [
            'search_id' => $bookingData['search_id'],
            'flight_id' => $bookingData['flight_id'],
            'passengers' => $request->input('passengers'),
            'contact' => $request->input('contact')
        ];
        
        // Save booking
        $bookingResult = $this->bookingService->saveBooking($bookingParams);
        
        if (!$bookingResult || empty($bookingResult['booking_id'])) {
            return redirect()->back()->with('error', 'Booking failed. Please try again.');
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
