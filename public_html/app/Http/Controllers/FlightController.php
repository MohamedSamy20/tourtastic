<?php

namespace App\Http\Controllers;

use App\Services\Flights\SeeruFlightSearchService;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    protected $flightService;

    public function __construct(SeeruFlightSearchService $flightService)
    {
        $this->flightService = $flightService;
    }

    public function search(Request $request)
    {
        try {
            $params = [
                'from_where' => $request->input('from_where'),
                'to_where' => $request->input('to_where'),
                'start' => $request->input('start'),
                'end' => $request->input('end'),
                'date' => $request->input('date'),
                'seat_type' => $request->input('seat_type'),
            ];

            $flights = $this->flightService->searchFlights($params);

            return view('flight.search', compact('flights'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getSearchResult(Request $request)
    {
        try {
            $searchId = $request->input('search_id');
            $result = $this->flightService->getSearchResult($searchId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkFare(Request $request)
    {
        $bookingData = $request->input('booking');
        if (!$bookingData) {
            return response()->json(['error' => 'Missing booking data'], 400);
        }

        try {
            $result = $this->flightService->checkFare(['booking' => $bookingData]);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function saveBooking(Request $request)
    {
        $bookingData = $request->input('booking');
        $passengers = $request->input('passengers');
        $contact = $request->input('contact');

        if (!$bookingData || !$passengers || !$contact) {
            return response()->json(['error' => 'Missing booking, passengers or contact data'], 400);
        }

        try {
            $params = [
                'booking' => $bookingData,
                'passengers' => $passengers,
                'contact' => $contact,
            ];
            $result = $this->flightService->saveBooking($params);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOrderDetails(Request $request)
    {
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response()->json(['error' => 'Missing order_id'], 400);
        }

        try {
            $result = $this->flightService->getOrderDetails(['order_id' => $orderId]);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancelOrder(Request $request)
    {
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response()->json(['error' => 'Missing order_id'], 400);
        }

        try {
            $result = $this->flightService->cancelOrder(['order_id' => $orderId]);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function issueOrder(Request $request)
    {
        $orderId = $request->input('order_id');
        if (!$orderId) {
            return response()->json(['error' => 'Missing order_id'], 400);
        }

        try {
            $result = $this->flightService->issueTicket($orderId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTicketDetails(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        if (!$ticketId) {
            return response()->json(['error' => 'Missing ticket_id'], 400);
        }

        try {
            $result = $this->flightService->getTicketDetails(['ticket_id' => $ticketId]);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function retrieveTicket(Request $request)
    {
        $airlinePnr = $request->input('airline_pnr');
        $lastName = $request->input('last_name');

        if (!$airlinePnr || !$lastName) {
            return response()->json(['error' => 'Missing airline_pnr or last_name'], 400);
        }

        try {
            $params = [
                'airline_pnr' => $airlinePnr,
                'last_name' => $lastName,
            ];
            $result = $this->flightService->retrieveTicket($params);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function refundTicket(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $legs = $request->input('legs', []);
        $totalFees = $request->input('total_fees', null);
        $passengers = $request->input('passengers', []);

        if (!$ticketId) {
            return response()->json(['error' => 'Missing ticket_id'], 400);
        }

        try {
            $params = [
                'ticket_id' => $ticketId,
                'legs' => $legs,
                'total_fees' => $totalFees,
                'passengers' => $passengers,
            ];
            $result = $this->flightService->refundTicket($params);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function voidTicket(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $passengers = $request->input('passengers', []);

        if (!$ticketId) {
            return response()->json(['error' => 'Missing ticket_id'], 400);
        }

        try {
            $params = [
                'ticket_id' => $ticketId,
                'passengers' => $passengers,
            ];
            $result = $this->flightService->voidTicket($params);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exchangeTicket(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $exchangeLegs = $request->input('exchange_legs', []);
        $totalFees = $request->input('total_fees', null);
        $passengers = $request->input('passengers', []);

        if (!$ticketId) {
            return response()->json(['error' => 'Missing ticket_id'], 400);
        }

        try {
            $params = [
                'ticket_id' => $ticketId,
                'exchange_legs' => $exchangeLegs,
                'total_fees' => $totalFees,
                'passengers' => $passengers,
            ];
            $result = $this->flightService->exchangeTicket($params);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
