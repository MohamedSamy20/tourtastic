<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeeruTestController;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */
Route::get('/intro','LandingpageController@index');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/install/check-db', 'HomeController@checkConnectDatabase');

// Social Login
Route::get('social-login/{provider}', 'Auth\LoginController@socialLogin');
Route::get('social-callback/{provider}', 'Auth\LoginController@socialCallBack');

// Logs
Route::get(config('admin.admin_route_prefix').'/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware(['auth', 'dashboard','system_log_view'])->name('admin.logs');

Route::get('/install','InstallerController@redirectToRequirement')->name('LaravelInstaller::welcome');
Route::get('/install/environment','InstallerController@redirectToWizard')->name('LaravelInstaller::environment');
require __DIR__ . '/../modules/Flight/Routes/web.php';
Route::get('/flight', [FlightController::class, 'search']);

// Flight Routes
// Group for Seeru specific routes, including tests
Route::group(['prefix' => 'seeru'], function() {
    // --- Test Endpoints for SeeruFlightSearchService --- 
    // Note: These endpoints directly call the service methods for testing purposes.
    // Use GET for search and result retrieval, POST for actions like fare check, booking, ticketing.

    // Test Flight Search (GET)
    // Example: /api/seeru/test-search?origin=DXB&destination=LHR&departure_date=2025-06-15&adults=1
    Route::get('/search', [SeeruTestController::class, 'testSearch']);

    // Test Get Search Result (GET)
    // Example: /api/seeru/test-result/{searchId}
    Route::get('/result/{searchId}', [SeeruTestController::class, 'testGetResult']);

    // Test Fare Check (POST)
    // Expects JSON body: { "search_id": "...", "result_id": "..." }
    Route::post('/fare-check', [SeeruTestController::class, 'testCheckFare']);

    // Test Save Booking (POST)
    // Expects JSON body with full booking details: { "fare_id": "...", "passengers": [...], "contact": {...} }
    Route::post('/save-booking', [SeeruTestController::class, 'testSaveBooking']);

 

    // Test Get Order Details (POST)
    Route::post('/order-details', [SeeruTestController::class, 'testOrderDetails']);

    // Test Cancel Order (POST)
    Route::post('/cancel-order', [SeeruTestController::class, 'testCancelOrder']);

    // Test Issue Ticket (POST)
    Route::post('/issue-order', [SeeruTestController::class, 'testIssueOrder']);

    // Test Ticket Endpoints (POST)
    Route::post('/ticket-details', [SeeruTestController::class, 'testTicketDetails']);
    Route::post('/ticket-retrieve', [SeeruTestController::class, 'testTicketRetrieve']);
    Route::post('/ticket-refund', [SeeruTestController::class, 'testTicketRefund']);
    Route::post('/ticket-void', [SeeruTestController::class, 'testTicketVoid']);
    Route::post('/ticket-exchange', [SeeruTestController::class, 'testTicketExchange']);
});


Route::post('/webhook/seeru', function (Request $request) {
    Log::info('Seeru Webhook Received:', $request->all());

    // Here, you can store or process data as you like.
    return response()->json(['status' => 'Webhook received successfully']);
});