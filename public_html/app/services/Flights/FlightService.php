<?php

namespace App\Services\Flights;

use App\Models\FlightProvider;
use Illuminate\Support\Facades\Log;

class FlightService implements FlightServiceInterface
{
    protected $provider;
    protected $service;
    
    /**
     * Create a new FlightService instance.
     */
    public function __construct()
    {
        $this->loadActiveProvider();
    }
    
    /**
     * Load the active flight provider from database
     */
    protected function loadActiveProvider()
    {
        try {
            // Get the active provider from database
            $this->provider = FlightProvider::where('enabled', true)->first();
            
            if (!$this->provider) {
                Log::error('No active flight provider found');
                return;
            }
            
            // Get the service class from the provider
            $serviceClass = $this->provider->service_class;
            
            // Check if the service class exists
            if (!class_exists($serviceClass)) {
                Log::error('Flight provider service class not found: ' . $serviceClass);
                return;
            }
            
            // Create an instance of the service class with provider config
            $this->service = new $serviceClass([
                'api_base_url' => $this->provider->api_base_url,
                'api_email' => $this->provider->api_email,
                'api_password' => $this->provider->api_password,
                'agency_code' => $this->provider->agency_code
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading flight provider: ' . $e->getMessage());
        }
    }
    
    /**
     * Search for flights
     * 
     * @param array $params Search parameters
     * @return array|null
     */
    public function searchFlights($params)
    {
        return $this->service ? $this->service->searchFlights($params) : null;
    }
    
    /**
     * Get search results by search ID
     * 
     * @param string $searchId Search ID
     * @return array|null
     */
    public function getSearchResult($searchId)
    {
        return $this->service ? $this->service->getSearchResult($searchId) : null;
    }
    
    /**
     * Check fare validity before booking
     * 
     * @param array $params Fare parameters
     * @return array|null
     */
    public function checkFare($params)
    {
        return $this->service ? $this->service->checkFare($params) : null;
    }
    
    /**
     * Save booking (hold or ready for issuance)
     * 
     * @param array $params Booking parameters
     * @return array|null
     */
    public function saveBooking($params)
    {
        return $this->service ? $this->service->saveBooking($params) : null;
    }
    
    /**
     * Issue ticket for an order
     * 
     * @param string $orderId Order ID
     * @return array|null
     */
    public function issueTicket($orderId)
    {
        return $this->service ? $this->service->issueTicket($orderId) : null;
    }
    
    /**
     * Cancel a booking
     * 
     * @param string $bookingId Booking ID
     * @return array|null
     */
    public function cancelBooking($bookingId)
    {
        return $this->service ? $this->service->cancelBooking($bookingId) : null;
    }
    
    /**
     * Request refund for a ticket
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function requestRefund($ticketNumber)
    {
        return $this->service ? $this->service->requestRefund($ticketNumber) : null;
    }
    
    /**
     * Void a ticket
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function voidTicket($ticketNumber)
    {
        return $this->service ? $this->service->voidTicket($ticketNumber) : null;
    }
    
    /**
     * Retrieve ticket information
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function retrieveTicket($ticketNumber)
    {
        return $this->service ? $this->service->retrieveTicket($ticketNumber) : null;
    }
}
