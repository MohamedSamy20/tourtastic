<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeeruTestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Group for Seeru specific routes, including tests
Route::group(['prefix' => 'seeru'], function() {
    // --- Test Endpoints for SeeruFlightSearchService --- 
    // Note: These endpoints directly call the service methods for testing purposes.
    // Use GET for search and result retrieval, POST for actions like fare check, booking, ticketing.

    // Test Flight Search (GET)
    // Example: /api/seeru/test-search?origin=DXB&destination=LHR&departure_date=2025-06-15&adults=1
    Route::get('/test-search', [SeeruTestController::class, 'testSearch']);

    // Test Get Search Result (GET)
    // Example: /api/seeru/test-result/{searchId}
    Route::get('/test-result/{searchId}', [SeeruTestController::class, 'testGetResult']);

    // Test Fare Check (POST)
    // Expects JSON body: { "search_id": "...", "result_id": "..." }
    Route::post('/test-fare-check', [SeeruTestController::class, 'testCheckFare']);

    // Test Save Booking (POST)
    // Expects JSON body with full booking details: { "fare_id": "...", "passengers": [...], "contact": {...} }
    Route::post('/test-save-booking', [SeeruTestController::class, 'testSaveBooking']);

 

    // Test Get Order Details (POST)
    Route::post('/test-order-details', [SeeruTestController::class, 'testOrderDetails']);

    // Test Cancel Order (POST)
    Route::post('/test-cancel-order', [SeeruTestController::class, 'testCancelOrder']);

    // Test Issue Ticket (POST)
    Route::post('/test-issue-order', [SeeruTestController::class, 'testIssueOrder']);

    // Test Ticket Endpoints (POST)
    Route::post('/test-ticket-details', [SeeruTestController::class, 'testTicketDetails']);
    Route::post('/test-ticket-retrieve', [SeeruTestController::class, 'testTicketRetrieve']);
    Route::post('/test-ticket-refund', [SeeruTestController::class, 'testTicketRefund']);
    Route::post('/test-ticket-void', [SeeruTestController::class, 'testTicketVoid']);
    Route::post('/test-ticket-exchange', [SeeruTestController::class, 'testTicketExchange']);

    // --- Original Application Endpoints (using FlightController) --- 
    // These likely map to the actual application logic, not direct service tests.
    // Keep these as they were unless instructed otherwise.
    Route::post('/search', 'App\Http\Controllers\FlightController@search');
    Route::post('/result/{searchId}', 'App\Http\Controllers\FlightController@getResult');
    Route::post('/booking/fare', 'App\Http\Controllers\FlightController@checkFare');
    Route::post('/booking/save', 'App\Http\Controllers\FlightController@saveBooking');
    Route::post('/order/details', 'App\Http\Controllers\FlightController@orderDetails');
    Route::post('/order/cancel', 'App\Http\Controllers\FlightController@cancelOrder');
    Route::post('/order/issue', 'App\Http\Controllers\FlightController@issueOrder');
    Route::post('/ticket/details', 'App\Http\Controllers\FlightController@ticketDetails');
    Route::post('/ticket/retrieve', 'App\Http\Controllers\FlightController@ticketRetrieve');
    Route::post('/ticket/refund', 'App\Http\Controllers\FlightController@ticketRefund');
    Route::post('/ticket/void', 'App\Http\Controllers\FlightController@ticketVoid');
    Route::post('/ticket/exchange', 'App\Http\Controllers\FlightController@ticketExchange');
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

