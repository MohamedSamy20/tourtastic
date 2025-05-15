<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Flights\SeeruFlightSearchService; // Use the correct service
use Illuminate\Support\Facades\Log; // Add Log facade
use Illuminate\Validation\ValidationException;

class SeeruTestController extends Controller
{
    protected $flightSearchService;

    // Use Dependency Injection to get the correct service instance
    public function __construct(SeeruFlightSearchService $flightSearchService)
    {
        $this->flightSearchService = $flightSearchService;
    }

    // ... [testSearch and testGetResult methods remain the same] ...

    /**
     * Test the flight search functionality.
     * Expects GET parameters like origin, destination, departure_date, adults, etc.
     */
    public function search(Request $request)
    {
        // Parameters expected by SeeruFlightSearchService (flat array)
        $params = [
            'adults' => $request->input('adults', 1),
            'children' => $request->input('children', 0),
            'infants' => $request->input('infants', 0),
            'origin' => $request->input('origin', 'DXB'),
            'destination' => $request->input('destination', 'LHR'),
            'departure_date' => $request->input('departure_date', now()->addDays(30)->format('Y-m-d')),
            'return_date' => $request->input('return_date'), // Optional
            'cabin_class' => $request->input('cabin_class', 'economy'),
            'direct_flights' => $request->input('direct_flights'), // Optional
        ];

        try {
            Log::info('SeeruTestController: Initiating testSearch', ['params' => $params]);
            $results = $this->flightSearchService->searchFlights($params);
            Log::info('SeeruTestController: testSearch successful', ['results' => $results]);

            return response()->json([
                'message' => 'Search successful!',
                'search_id' => $results['search_id'] ?? null,
                'params_used' => $params,
                'results' => $results
            ]);

        } catch (\InvalidArgumentException $e) {
            Log::warning('SeeruTestController: Invalid search parameters', ['error' => $e->getMessage(), 'params' => $params]);
            return response()->json(['error' => 'Invalid search parameters.', 'message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            Log::error('SeeruTestController: testSearch Exception', [
                'message' => $e->getMessage(),
                'params' => $params,
                // 'trace' => $e->getTraceAsString() // Avoid logging full trace
            ]);
            return response()->json([
                'error' => 'Search request failed.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test retrieving search results using a search ID.
     * Expects searchId from the URL path.
     */
    public function getResult(Request $request, $searchId)
    {
        if (empty($searchId)) {
            return response()->json(['error' => 'Missing search ID.'], 400);
        }

        try {
            Log::info('SeeruTestController: Initiating testGetResult', ['searchId' => $searchId]);
            $results = $this->flightSearchService->getSearchResult($searchId);
            Log::info('SeeruTestController: testGetResult successful', ['searchId' => $searchId]);

            // Return the full results which contains the 'result' array needed for fare check
            return response()->json([
                'message' => 'Get result successful!',
                'search_id' => $searchId,
                'results' => $results // This should contain the 'result' array
            ]);

        } catch (\Exception $e) {
            Log::error('SeeruTestController: testGetResult Exception', [
                'message' => $e->getMessage(),
                'searchId' => $searchId,
            ]);
            return response()->json([
                'error' => 'Get result request failed.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Test the fare check functionality.
     * Expects POST body with the full booking object under the 'booking' key.
     */
    public function checkFare(Request $request)
    {
        $params = $request->all(); // Expects {"booking": { ... full object ... }}

        // *** UPDATED VALIDATION: Check for the 'booking' key and ensure it's an array/object ***
        if (!isset($params['booking']) || !is_array($params['booking'])) {
             return response()->json(['error' => 'Missing or invalid required parameter: booking (must be the full booking object from search results).'], 400);
        }
        // Optional: Add deeper validation if needed, e.g., check for booking.fare_key
        if (!isset($params['booking']['fare_key'])) {
             // Log warning but proceed, API might handle it
             Log::warning('SeeruTestController: testCheckFare - booking object missing fare_key.', ['params_keys' => array_keys($params)]);
        }

        try {
            Log::info('SeeruTestController: Initiating testCheckFare', ['params_keys' => array_keys($params)]);
            // Pass the entire received structure {"booking": {...}} to the service
            $results = $this->flightSearchService->checkFare($params);
            Log::info('SeeruTestController: testCheckFare successful');

            return response()->json([
                'message' => 'Fare check successful!',
                'params_used' => ['booking object provided'], // Avoid logging potentially large object
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('SeeruTestController: testCheckFare Exception', [
                'message' => $e->getMessage(),
                'params_keys' => array_keys($params),
            ]);
            // Return the specific error message from the service/API
            return response()->json([
                'error' => 'Fare check request failed.',
                'message' => $e->getMessage(),
            ], 500); // Keep 500 as it's an internal error triggering the API call failure
        }
    }

    /**
     * Test the booking save functionality.
     * Expects POST body with booking details (passengers, contact, fare_id, etc.).
     * NOTE: This likely also needs the full booking object based on API docs.
     * TODO: Update this method similarly to testCheckFare if needed.
     */
public function saveBooking(Request $request)
{
    $params = $request->all();

    // Validate presence of 'booking' key
    if (!isset($params['booking']) || !is_array($params['booking'])) {
        return response()->json(['error' => 'Missing or invalid booking data.'], 400);
    }

    try {
        Log::info('SeeruTestController: Initiating testSaveBooking', ['params_keys' => array_keys($params)]);
        $results = $this->flightSearchService->saveBooking($params);
        Log::info('SeeruTestController: testSaveBooking successful');

        return response()->json([
            'message' => 'Booking saved successfully!',
            'results' => $results,
        ]);

    } catch (\Exception $e) {
        Log::error('SeeruTestController: testSaveBooking Exception', [
            'message' => $e->getMessage(),
            'params_keys' => array_keys($params),
        ]);
        return response()->json([
            'error' => 'Booking save request failed.',
            'message' => $e->getMessage(),
        ], 500);
    }
}

    public function orderDetails(Request $request)
    {
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response()->json(['error' => 'Missing order_id'], 400);
        }
        try {
            $result = $this->flightSearchService->getOrderDetails($orderId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Order details failed.', 'message' => $e->getMessage()], 500);
        }
    }

    public function cancelOrder(Request $request)
    {
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response()->json(['error' => 'Missing order_id'], 400);
        }
        try {
            $result = $this->flightSearchService->cancelOrder($orderId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Order cancel failed.', 'message' => $e->getMessage()], 500);
        }
    }

    public function issueOrder(Request $request)
    {
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response()->json(['error' => 'Missing order_id'], 400);
        }
        try {
            $result = $this->flightSearchService->issueTicket($orderId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Order issue failed.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /ticket/details
     * Get ticket details by ticket_id
     */
    public function ticketDetails(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        if (!$ticketId) {
            return response()->json(['error' => 'Missing ticket_id'], 400);
        }
        try {
            $result = $this->flightSearchService->getTicketDetails($ticketId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ticket details failed.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /ticket/retrieve
     * Retrieve ticket details by airline_pnr and last_name
     */
    public function ticketRetrieve(Request $request)
    {
        $airlinePnr = $request->input('airline_pnr');
        $lastName = $request->input('last_name');
        if (!$airlinePnr || !$lastName) {
            return response()->json(['error' => 'Missing airline_pnr or last_name'], 400);
        }
        try {
            $result = $this->flightSearchService->retrieveTicket($airlinePnr, $lastName);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ticket retrieve failed.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /ticket/refund
     * Initiate a refund process for an issued ticket
     */
    public function ticketRefund(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $legs = $request->input('legs', []);
        $passengers = $request->input('passengers', []);
        $totalFees = $request->input('total_fees');
        if (!$ticketId) {
            return response()->json(['error' => 'Missing ticket_id'], 400);
        }
        try {
            $result = $this->flightSearchService->refundTicket($ticketId, $legs, $passengers, $totalFees);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ticket refund failed.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /ticket/void
     * Initiate a void process for an issued ticket
     */
    public function ticketVoid(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $passengers = $request->input('passengers', []);
        if (!$ticketId) {
            return response()->json(['error' => 'Missing ticket_id'], 400);
        }
        try {
            $result = $this->flightSearchService->voidTicket($ticketId, $passengers);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ticket void failed.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /ticket/exchange
     * Initiate an exchange process for an issued ticket
     */
    public function ticketExchange(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $exchangeLegs = $request->input('exchange_legs', []);
        $passengers = $request->input('passengers', []);
        $totalFees = $request->input('total_fees');
        if (!$ticketId) {
            return response()->json(['error' => 'Missing ticket_id'], 400);
        }
        try {
            $result = $this->flightSearchService->exchangeTicket($ticketId, $exchangeLegs, $passengers, $totalFees);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ticket exchange failed.', 'message' => $e->getMessage()], 500);
        }
    }
}

