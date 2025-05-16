<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Flights\SeeruFlightSearchService;
use Illuminate\Support\Facades\Log;

class FlightController extends Controller
{
    protected $flightSearchService;

    public function __construct(SeeruFlightSearchService $flightSearchService)
    {
        $this->flightSearchService = $flightSearchService;
    }

    /**
     * Display the main flight search form page.
     */
    public function showSearchForm(Request $request)
    {
        // Pass any initial search parameters from the URL (e.g., from home page quick search)
        $initialSearchParams = $request->only(["origin", "destination", "departure_date", "return_date", "adults", "children", "infants", "cabin_class"]);
        return view("flights.index", ["initialSearchParams" => $initialSearchParams]);
    }

    public function search(Request $request)
    {
        $params = [
            "adults" => $request->input("adults", 1),
            "children" => $request->input("children", 0),
            "infants" => $request->input("infants", 0),
            "origin" => $request->input("origin"),
            "destination" => $request->input("destination"),
            "departure_date" => $request->input("departure_date"),
            "return_date" => $request->input("return_date"),
            "cabin_class" => $request->input("cabin_class", "economy"),
            "direct_flights" => $request->input("direct_flights"),
        ];

        // Basic validation for required fields
        if (empty($params["origin"]) || empty($params["destination"]) || empty($params["departure_date"])) {
            return response()->json(["error" => "Missing required search parameters: Origin, Destination, and Departure Date are required."], 400);
        }

        try {
            Log::info("FlightController: Initiating flight search", ["params" => $params]);
            $results = $this->flightSearchService->searchFlights($params);
            // Ensure results are properly structured for the frontend, especially the search_id
            if (!isset($results["search_id"])) {
                 Log::warning("FlightController: searchFlights service did not return a search_id", ["results" => $results]);
                 // Depending on Seeru API, an empty result might be valid or an error.
                 // For now, let's assume an empty result set is possible and not an error itself if search_id is missing.
            }
            Log::info("FlightController: Flight search successful", ["search_id" => $results["search_id"] ?? null]);

            return response()->json([
                "message" => "Search successful!",
                "search_id" => $results["search_id"] ?? null,
                "params_used" => $params,
                "results" => $results // This should contain the flight offers or a way to get them via search_id
            ]);

        } catch (\InvalidArgumentException $e) {
            Log::warning("FlightController: Invalid search parameters", ["error" => $e->getMessage(), "params" => $params]);
            return response()->json(["error" => "Invalid search parameters.", "message" => $e->getMessage()], 400);
        } catch (\Exception $e) {
            Log::error("FlightController: Flight search Exception", [
                "message" => $e->getMessage(),
                "params" => $params,
            ]);
            return response()->json([
                "error" => "Search request failed.",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function getResult(Request $request, $searchId)
    {
        if (empty($searchId)) {
            return response()->json(["error" => "Missing search ID."], 400);
        }

        try {
            Log::info("FlightController: Initiating getResult", ["searchId" => $searchId]);
            $results = $this->flightSearchService->getSearchResult($searchId);
            Log::info("FlightController: getResult successful", ["searchId" => $searchId]);

            return response()->json([
                "message" => "Get result successful!",
                "search_id" => $searchId,
                "results" => $results
            ]);

        } catch (\Exception $e) {
            Log::error("FlightController: getResult Exception", [
                "message" => $e->getMessage(),
                "searchId" => $searchId,
            ]);
            return response()->json([
                "error" => "Get result request failed.",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function checkFare(Request $request)
    {
        $params = $request->all();

        if (!isset($params["booking"]) || !is_array($params["booking"])) {
             return response()->json(["error" => "Missing or invalid required parameter: booking."], 400);
        }
        // Assuming fare_key or similar identifier is within the booking object for the service
        // if (!isset($params["booking"]["fare_key"])) {
        //      Log::warning("FlightController: checkFare - booking object missing fare_key.", ["params_keys" => array_keys($params["booking"])]);
        // }

        try {
            Log::info("FlightController: Initiating checkFare", ["booking_param_keys" => array_keys($params["booking"])]);
            $results = $this->flightSearchService->checkFare($params); // Service expects the full booking object
            Log::info("FlightController: checkFare successful");

            return response()->json([
                "message" => "Fare check successful!",
                "results" => $results // This should contain the confirmed fare details
            ]);

        } catch (\Exception $e) {
            Log::error("FlightController: checkFare Exception", [
                "message" => $e->getMessage(),
                "booking_param_keys" => array_keys($params["booking"])]);
            return response()->json([
                "error" => "Fare check request failed.",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function saveBooking(Request $request)
    {
        $params = $request->all(); // Expects { "fare_id": "...", "passengers": [...], "contact": {...} } or similar structure

        // Add validation for required booking parameters based on Seeru API needs
        // Example: if (!isset($params["fare_id"]) || !isset($params["passengers"])) {
        //     return response()->json(["error" => "Missing required booking data (fare_id, passengers)."], 400);
        // }

        try {
            Log::info("FlightController: Initiating saveBooking", ["params_keys" => array_keys($params)]);
            $results = $this->flightSearchService->saveBooking($params);
            Log::info("FlightController: saveBooking successful");

            return response()->json([
                "message" => "Booking saved successfully!",
                "results" => $results, // This should contain order_id or booking confirmation
            ]);

        } catch (\Exception $e) {
            Log::error("FlightController: saveBooking Exception", [
                "message" => $e->getMessage(),
                "params_keys" => array_keys($params),
            ]);
            return response()->json([
                "error" => "Booking save request failed.",
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function orderDetails(Request $request)
    {
        $orderId = $request->input("order_id");
        if (!$orderId) {
            return response()->json(["error" => "Missing order_id"], 400);
        }
        try {
            Log::info("FlightController: Getting order details", ["order_id" => $orderId]);
            $result = $this->flightSearchService->getOrderDetails($orderId);
            Log::info("FlightController: Got order details successfully", ["order_id" => $orderId]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("FlightController: Order details failed", ["order_id" => $orderId, "message" => $e->getMessage()]);
            return response()->json(["error" => "Order details failed.", "message" => $e->getMessage()], 500);
        }
    }

    public function cancelOrder(Request $request)
    {
        $orderId = $request->input("order_id");
        if (!$orderId) {
            return response()->json(["error" => "Missing order_id"], 400);
        }
        try {
            Log::info("FlightController: Cancelling order", ["order_id" => $orderId]);
            $result = $this->flightSearchService->cancelOrder($orderId);
            Log::info("FlightController: Cancelled order successfully", ["order_id" => $orderId]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("FlightController: Order cancel failed", ["order_id" => $orderId, "message" => $e->getMessage()]);
            return response()->json(["error" => "Order cancel failed.", "message" => $e->getMessage()], 500);
        }
    }

    public function issueOrder(Request $request)
    {
        $orderId = $request->input("order_id");
        if (!$orderId) {
            return response()->json(["error" => "Missing order_id"], 400);
        }
        try {
            Log::info("FlightController: Issuing ticket for order", ["order_id" => $orderId]);
            $result = $this->flightSearchService->issueTicket($orderId);
            Log::info("FlightController: Issued ticket successfully", ["order_id" => $orderId]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("FlightController: Order issue failed", ["order_id" => $orderId, "message" => $e->getMessage()]);
            return response()->json(["error" => "Order issue failed.", "message" => $e->getMessage()], 500);
        }
    }

    public function ticketDetails(Request $request)
    {
        $ticketId = $request->input("ticket_id");
        if (!$ticketId) {
            return response()->json(["error" => "Missing ticket_id"], 400);
        }
        try {
            Log::info("FlightController: Getting ticket details", ["ticket_id" => $ticketId]);
            $result = $this->flightSearchService->getTicketDetails($ticketId);
            Log::info("FlightController: Got ticket details successfully", ["ticket_id" => $ticketId]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("FlightController: Ticket details failed", ["ticket_id" => $ticketId, "message" => $e->getMessage()]);
            return response()->json(["error" => "Ticket details failed.", "message" => $e->getMessage()], 500);
        }
    }

    public function ticketRetrieve(Request $request)
    {
        $airlinePnr = $request->input("airline_pnr");
        $lastName = $request->input("last_name");
        if (!$airlinePnr || !$lastName) {
            return response()->json(["error" => "Missing airline_pnr or last_name"], 400);
        }
        try {
            Log::info("FlightController: Retrieving ticket", ["airline_pnr" => $airlinePnr, "last_name" => $lastName]);
            $result = $this->flightSearchService->retrieveTicket($airlinePnr, $lastName);
            Log::info("FlightController: Retrieved ticket successfully", ["airline_pnr" => $airlinePnr]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("FlightController: Ticket retrieve failed", ["airline_pnr" => $airlinePnr, "message" => $e->getMessage()]);
            return response()->json(["error" => "Ticket retrieve failed.", "message" => $e->getMessage()], 500);
        }
    }

    public function ticketRefund(Request $request)
    {
        $ticketId = $request->input("ticket_id");
        $legs = $request->input("legs", []);
        $passengers = $request->input("passengers", []);
        $totalFees = $request->input("total_fees");
        if (!$ticketId) {
            return response()->json(["error" => "Missing ticket_id"], 400);
        }
        try {
            Log::info("FlightController: Refunding ticket", ["ticket_id" => $ticketId]);
            $result = $this->flightSearchService->refundTicket($ticketId, $legs, $passengers, $totalFees);
            Log::info("FlightController: Refunded ticket successfully", ["ticket_id" => $ticketId]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("FlightController: Ticket refund failed", ["ticket_id" => $ticketId, "message" => $e->getMessage()]);
            return response()->json(["error" => "Ticket refund failed.", "message" => $e->getMessage()], 500);
        }
    }

    public function ticketVoid(Request $request)
    {
        $ticketId = $request->input("ticket_id");
        $passengers = $request->input("passengers", []);
        if (!$ticketId) {
            return response()->json(["error" => "Missing ticket_id"], 400);
        }
        try {
            Log::info("FlightController: Voiding ticket", ["ticket_id" => $ticketId]);
            $result = $this->flightSearchService->voidTicket($ticketId, $passengers);
            Log::info("FlightController: Voided ticket successfully", ["ticket_id" => $ticketId]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("FlightController: Ticket void failed", ["ticket_id" => $ticketId, "message" => $e->getMessage()]);
            return response()->json(["error" => "Ticket void failed.", "message" => $e->getMessage()], 500);
        }
    }

    public function ticketExchange(Request $request)
    {
        $ticketId = $request->input("ticket_id");
        $exchangeLegs = $request->input("exchange_legs", []);
        $passengers = $request->input("passengers", []);
        $totalFees = $request->input("total_fees");
        if (!$ticketId) {
            return response()->json(["error" => "Missing ticket_id"], 400);
        }
        try {
            Log::info("FlightController: Exchanging ticket", ["ticket_id" => $ticketId]);
            $result = $this->flightSearchService->exchangeTicket($ticketId, $exchangeLegs, $passengers, $totalFees);
            Log::info("FlightController: Exchanged ticket successfully", ["ticket_id" => $ticketId]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("FlightController: Ticket exchange failed", ["ticket_id" => $ticketId, "message" => $e->getMessage()]);
            return response()->json(["error" => "Ticket exchange failed.", "message" => $e->getMessage()], 500);
        }
    }
}

