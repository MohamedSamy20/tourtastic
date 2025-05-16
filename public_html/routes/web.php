<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlightController; // Changed to FlightController
// use App\Http\Controllers\SeeruTestController; // Commented out or remove if not used elsewhere in this file

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
Route::get("/intro", "LandingpageController@index");
Route::get("/", "HomeController@index");
Route::get("/home", "HomeController@index")->name("home");
Route::post("/install/check-db", "HomeController@checkConnectDatabase");

// Social Login
Route::get("social-login/{provider}", "Auth\LoginController@socialLogin");
Route::get("social-callback/{provider}", "Auth\LoginController@socialCallBack");

// Logs
Route::get(config("admin.admin_route_prefix")."/logs", "\Rap2hpoutre\LaravelLogViewer\LogViewerController@index")->middleware(["auth", "dashboard","system_log_view"])->name("admin.logs");

Route::get("/install", "InstallerController@redirectToRequirement")->name("LaravelInstaller::welcome");
Route::get("/install/environment", "InstallerController@redirectToWizard")->name("LaravelInstaller::environment");
require __DIR__ . "/../modules/Flight/Routes/web.php"; // This might contain other flight routes

// Dedicated flights page route
Route::get('/flights', [FlightController::class, 'showSearchForm'])->name('flights.index');

// Production Flight API-like Routes (used by AJAX)
Route::group(["prefix" => "flight", "as" => "flight."], function () {
    // Flight Search & Results
    Route::get("/search", [FlightController::class, "search"])->name("search"); // Called by AJAX
    Route::get("/results/{searchId}", [FlightController::class, "getResult"])->name("results"); // Called by AJAX

    // Fare & Booking Process
    Route::post("/fare-check", [FlightController::class, "checkFare"])->name("fare.check"); // Called by AJAX
    Route::post("/book", [FlightController::class, "saveBooking"])->name("book.save"); // Called by AJAX

    // Order Management
    Route::post("/order/details", [FlightController::class, "orderDetails"])->name("order.details"); // Called by AJAX
    Route::post("/order/cancel", [FlightController::class, "cancelOrder"])->name("order.cancel"); // Called by AJAX
    Route::post("/order/issue", [FlightController::class, "issueOrder"])->name("order.issue"); // Called by AJAX

    // Ticket Management
    Route::post("/ticket/details", [FlightController::class, "ticketDetails"])->name("ticket.details"); // Called by AJAX
    Route::post("/ticket/retrieve", [FlightController::class, "ticketRetrieve"])->name("ticket.retrieve"); // Called by AJAX
    Route::post("/ticket/refund", [FlightController::class, "ticketRefund"])->name("ticket.refund"); // Called by AJAX
    Route::post("/ticket/void", [FlightController::class, "ticketVoid"])->name("ticket.void"); // Called by AJAX
    Route::post("/ticket/exchange", [FlightController::class, "ticketExchange"])->name("ticket.exchange"); // Called by AJAX
});


Route::post("/webhook/seeru", function (Request $request) {
    Log::info("Seeru Webhook Received:", $request->all());

    // Here, you can store or process data as you like.
    return response()->json(["status" => "Webhook received successfully"]);
});


