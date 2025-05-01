<?php

namespace App\Providers;

use App\Services\Flights\FlightService;
use App\Services\Flights\SeeruAuthService;
use App\Services\Flights\SeeruFlightSearchService;
use Illuminate\Support\ServiceProvider;

class FlightServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SeeruAuthService::class, function ($app) {
            return new SeeruAuthService();
        });

        $this->app->singleton(SeeruFlightSearchService::class, function ($app) {
            return new SeeruFlightSearchService();
        });

        $this->app->singleton(FlightService::class, function ($app) {
            return new FlightService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
