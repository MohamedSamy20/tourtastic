<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlightController;

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
Route::get('/flight/search', [FlightController::class, 'search'])->name('flight.search');
Route::get('/flight/search-result', [FlightController::class, 'getSearchResult'])->name('flight.search-result');

Route::prefix('test/seeru')->group(function () {
    Route::get('search', [App\Http\Controllers\SeeruTestController::class, 'testSearch']);
    Route::get('result', [App\Http\Controllers\SeeruTestController::class, 'testSearchResult']);
    Route::post('fare', [App\Http\Controllers\SeeruTestController::class, 'testFareCheck']);
    Route::post('booking', [App\Http\Controllers\SeeruTestController::class, 'testSaveBooking']);
    Route::post('issue', [App\Http\Controllers\SeeruTestController::class, 'testIssueTicket']);
});