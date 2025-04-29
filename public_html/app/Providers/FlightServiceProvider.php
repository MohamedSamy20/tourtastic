<?php

namespace App\Providers;

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
        $this->app->singleton(SeeruFlightSearchService::class, function ($app) {
            return new SeeruFlightSearchService();
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
