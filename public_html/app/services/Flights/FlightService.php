<?php

namespace App\Services\Flights;

use App\Models\FlightProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Container\BindingResolutionException;

class FlightService
{
    protected $activeProviderService;

    public function __construct()
    {
        $this->activeProviderService = $this->resolveActiveProvider();
    }

    /**
     * Resolve the active flight provider service based on database configuration.
     *
     * @return object|null The instantiated service class or null if none is active/found.
     */
    protected function resolveActiveProvider()
    {
        try {
            // Find the first enabled provider
            $provider = FlightProvider::enabled()->first();

            if (!$provider) {
                Log::warning("No active flight provider found in the 'flight_providers' table.");
                return null;
            }

            $serviceClass = $provider->service_class;

            if (!class_exists($serviceClass)) {
                Log::error("Active flight provider service class '{$serviceClass}' not found.");
                return null;
            }

            // Instantiate the service class using Laravel's service container
            // This allows for dependency injection within the specific provider service
            return app()->make($serviceClass);

        } catch (BindingResolutionException $e) {
            Log::error("Error resolving flight provider service class '{$serviceClass}': " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error("Error retrieving or instantiating active flight provider: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if an active provider service is available.
     *
     * @return bool
     */
    public function hasActiveProvider(): bool
    {
        return !is_null($this->activeProviderService);
    }

    /**
     * Dynamically call methods on the active provider service.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws \BadMethodCallException If no active provider or method doesn't exist.
     */
    public function __call(string $method, array $parameters)
    {
        if (!$this->hasActiveProvider()) {
            Log::error("Attempted to call method '{$method}' on FlightService, but no active provider is configured.");
            // Optionally, return a default error response or throw a specific exception
            // For now, returning null to avoid breaking execution flow unexpectedly
            return null; 
            // throw new \BadMethodCallException("No active flight provider configured.");
        }

        if (!method_exists($this->activeProviderService, $method)) {
            Log::error("Method '{$method}' does not exist on the active flight provider service: " . get_class($this->activeProviderService));
            // Optionally, return a default error response or throw a specific exception
            return null;
            // throw new \BadMethodCallException("Method {$method} does not exist on " . get_class($this->activeProviderService));
        }

        // Forward the call to the active provider's service instance
        return $this->activeProviderService->{$method}(...$parameters);
    }

    /**
     * Provide a way to get the underlying active service if needed, though __call is preferred.
     *
     * @return object|null
     */
    public function getActiveProviderService()
    {
        return $this->activeProviderService;
    }
}
