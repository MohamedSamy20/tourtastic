<?php

namespace App\Providers;

use App\Services\Flights\FlightService;
use App\Services\Flights\SeeruAuthService;
use App\Services\Flights\SeeruFlightSearchService;
use App\Services\Flights\SeeruBookingService; // Add Booking Service
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
        // Register SeeruAuthService first as it's a dependency
        $this->app->singleton(SeeruAuthService::class, function ($app) {
            // Pass config if needed, though it reads from Laravel config by default
            return new SeeruAuthService();
        });

        // Register SeeruFlightSearchService with dependency injection
        $this->app->singleton(SeeruFlightSearchService::class, function ($app) {
            // Inject the resolved SeeruAuthService instance
            return new SeeruFlightSearchService($app->make(SeeruAuthService::class));
        });

        // Register SeeruBookingService similarly (assuming it also needs AuthService)
        $this->app->singleton(SeeruBookingService::class, function ($app) {
            // Inject the resolved SeeruAuthService instance
            return new SeeruBookingService($app->make(SeeruAuthService::class));
        });

        // Keep the original FlightService registration if still used
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

