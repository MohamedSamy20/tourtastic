<?php

namespace App\Http\Controllers;

use App\services\Flights\SeeruFlightSearchService;
use App\services\Flights\SeeruBookingService;
use Illuminate\Http\Request;

class SeeruTestController extends Controller
{
    protected $searchService;
    protected $bookingService;
    
    public function __construct(SeeruFlightSearchService $searchService, SeeruBookingService $bookingService)
    {
        $this->searchService = $searchService;
        $this->bookingService = $bookingService;
    }
    
    public function testSearch()
    {
        $searchParams = [
            'trips' => 'oneway',
            'adults' => 1,
            'children' => 0,
            'infants' => 0,
            'cabin_class' => 'economy',
            'direct_flights' => false,
            'origin' => 'JED',
            'destination' => 'CAI',
            'departure_date' => '2025-05-01'
        ];
        
        $searchResults = $this->searchService->searchFlights($searchParams);
        
        return response()->json([
            'success' => !empty($searchResults),
            'data' => $searchResults
        ]);
    }
    
    public function testSearchResult(Request $request)
    {
        $searchId = $request->input('search_id');
        $results = $this->searchService->getSearchResult($searchId);
        
        return response()->json([
            'success' => !empty($results),
            'data' => $results
        ]);
    }
    
    public function testFareCheck(Request $request)
    {
        $fareParams = [
            'search_id' => $request->input('search_id'),
            'flight_id' => $request->input('flight_id')
        ];
        
        $fareResult = $this->bookingService->checkFare($fareParams);
        
        return response()->json([
            'success' => !empty($fareResult),
            'data' => $fareResult
        ]);
    }
    
    public function testSaveBooking(Request $request)
    {
        // هنا يمكنك استخدام بيانات من الطلب أو استخدام بيانات ثابتة للاختبار
        $bookingParams = [
            'search_id' => $request->input('search_id'),
            'flight_id' => $request->input('flight_id'),
            'passengers' => [
                [
                    'type' => 'ADT',
                    'title' => 'MR',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'gender' => 'M',
                    'date_of_birth' => '1990-01-01',
                    'passport_number' => 'AB123456',
                    'passport_expiry' => '2030-01-01',
                    'nationality' => 'US'
                ]
            ],
            'contact' => [
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                'address' => '123 Main St'
            ]
        ];
        
        $bookingResult = $this->bookingService->saveBooking($bookingParams);
        
        return response()->json([
            'success' => !empty($bookingResult),
            'data' => $bookingResult
        ]);
    }
    
    public function testIssueTicket(Request $request)
    {
        $orderId = $request->input('order_id');
        $ticketResult = $this->bookingService->issueTicket($orderId);
        
        return response()->json([
            'success' => !empty($ticketResult),
            'data' => $ticketResult
        ]);
    }
}
