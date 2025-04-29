<?php

namespace App\Services\Flights;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SeeruFlightsService implements FlightServiceInterface
{
    protected $client;
    protected $baseUrl;
    protected $email;
    protected $password;
    protected $agencyCode;
    
    /**
     * Create a new SeeruFlightsService instance.
     */
    public function __construct(array $config = null)
    {
        $this->client = new Client([
            'timeout' => 30,
            'http_errors' => false
        ]);
        
        // If config is provided, use it (for dynamic provider switching)
        // Otherwise use the config from services.php
        if ($config) {
            $this->baseUrl = $config['api_base_url'] ?? config('services.seeru.endpoint');
            $this->email = $config['api_email'] ?? config('services.seeru.email');
            $this->password = $config['api_password'] ?? config('services.seeru.password');
            $this->agencyCode = $config['agency_code'] ?? config('services.seeru.agency_code');
        } else {
            $this->baseUrl = config('services.seeru.endpoint');
            $this->email = config('services.seeru.email');
            $this->password = config('services.seeru.password');
            $this->agencyCode = config('services.seeru.agency_code');
        }
    }
    
    /**
     * Get JWT authentication token
     * 
     * @return string|null
     */
    public function getAuthToken()
    {
        $cacheKey = 'seeru_jwt_token_' . md5($this->email . $this->agencyCode);
        
        return Cache::remember($cacheKey, 3500, function () {
            try {
                $response = $this->client->post($this->baseUrl . '/auth/login', [
                    'json' => [
                        'email' => $this->email,
                        'password' => $this->password,
                        'agency_code' => $this->agencyCode
                    ],
                ]);
                
                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody(), true);
                    return $data['token'] ?? null;
                }
                
                Log::error('Seeru Auth Failed', [
                    'status' => $response->getStatusCode(),
                    'response' => (string)$response->getBody()
                ]);
                return null;
                
            } catch (\Exception $e) {
                Log::error('Seeru Auth Exception: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    /**
     * Make an authenticated API request to Seeru
     * 
     * @param string $method HTTP method (GET, POST, etc)
     * @param string $endpoint API endpoint
     * @param array $params Request parameters
     * @return array|null
     */
    protected function makeRequest($method, $endpoint, $params = [])
    {
        $token = $this->getAuthToken();
        
        if (!$token) {
            Log::error('Seeru API Request Failed: No auth token');
            return null;
        }
        
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ];
        
        // Add parameters based on request method
        if ($method === 'GET') {
            $options['query'] = $params;
        } else {
            $options['json'] = $params;
        }
        
        try {
            $response = $this->client->request($method, $this->baseUrl . $endpoint, $options);
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            }
            
            Log::error('Seeru API Request Failed', [
                'endpoint' => $endpoint,
                'status' => $response->getStatusCode(),
                'response' => (string)$response->getBody(),
                'params' => $params
            ]);
            return null;
            
        } catch (RequestException $e) {
            Log::error('Seeru API Exception: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'params' => $params
            ]);
            return null;
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
        // Construct the search endpoint based on parameters
        $trips = $params['trips'] ?? 'oneway';
        $adults = $params['adults'] ?? 1;
        $children = $params['children'] ?? 0;
        $infants = $params['infants'] ?? 0;
        
        $endpoint = "/search/{$trips}/{$adults}/{$children}/{$infants}";
        
        // Remove parameters used in the endpoint URL
        unset($params['trips'], $params['adults'], $params['children'], $params['infants']);
        
        return $this->makeRequest('GET', $endpoint, $params);
    }
    
    /**
     * Get search results by search ID
     * 
     * @param string $searchId Search ID
     * @return array|null
     */
    public function getSearchResult($searchId)
    {
        return $this->makeRequest('GET', "/result/{$searchId}");
    }
    
    /**
     * Check fare validity before booking
     * 
     * @param array $params Fare parameters
     * @return array|null
     */
    public function checkFare($params)
    {
        return $this->makeRequest('POST', '/booking/fare', $params);
    }
    
    /**
     * Save booking (hold or ready for issuance)
     * 
     * @param array $params Booking parameters
     * @return array|null
     */
    public function saveBooking($params)
    {
        return $this->makeRequest('POST', '/booking/save', $params);
    }
    
    /**
     * Issue ticket for an order
     * 
     * @param string $orderId Order ID
     * @return array|null
     */
    public function issueTicket($orderId)
    {
        return $this->makeRequest('POST', '/order/issue', ['order_id' => $orderId]);
    }
    
    /**
     * Cancel a booking
     * 
     * @param string $bookingId Booking ID
     * @return array|null
     */
    public function cancelBooking($bookingId)
    {
        return $this->makeRequest('POST', '/booking/cancel', ['booking_id' => $bookingId]);
    }
    
    /**
     * Request refund for a ticket
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function requestRefund($ticketNumber)
    {
        return $this->makeRequest('POST', '/ticket/refund', ['ticket_number' => $ticketNumber]);
    }
    
    /**
     * Void a ticket
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function voidTicket($ticketNumber)
    {
        return $this->makeRequest('POST', '/ticket/void', ['ticket_number' => $ticketNumber]);
    }
    
    /**
     * Retrieve ticket information
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function retrieveTicket($ticketNumber)
    {
        return $this->makeRequest('GET', "/ticket/{$ticketNumber}");
    }
}
