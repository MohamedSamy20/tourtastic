<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => 'seeru'], function() {
    Route::get('/test-search', 'App\Http\Controllers\SeeruTestController@testSearch');
    Route::get('/test-search-result', 'App\Http\Controllers\SeeruTestController@testSearchResult');
    Route::get('/test-fare-check', 'App\Http\Controllers\SeeruTestController@testFareCheck');
    Route::get('/test-save-booking', 'App\Http\Controllers\SeeruTestController@testSaveBooking');

    Route::post('/booking/fare', 'App\Http\Controllers\FlightController@checkFare');
    Route::post('/booking/save', 'App\Http\Controllers\FlightController@saveBooking');

    Route::post('/order/details', 'App\Http\Controllers\FlightController@getOrderDetails');
    Route::post('/order/cancel', 'App\Http\Controllers\FlightController@cancelOrder');
    Route::post('/order/issue', 'App\Http\Controllers\FlightController@issueOrder');

    Route::post('/ticket/details', 'App\Http\Controllers\FlightController@getTicketDetails');
    Route::post('/ticket/retrieve', 'App\Http\Controllers\FlightController@retrieveTicket');
    Route::post('/ticket/refund', 'App\Http\Controllers\FlightController@refundTicket');
    Route::post('/ticket/void', 'App\Http\Controllers\FlightController@voidTicket');
    Route::post('/ticket/exchange', 'App\Http\Controllers\FlightController@exchangeTicket');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
