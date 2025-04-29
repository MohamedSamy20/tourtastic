<?php

namespace App\Services\Flights;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class SeeruBookingService
{
    protected $client;
    protected $authService;
    protected $baseUrl;
    
    /**
     * Create a new SeeruBookingService instance.
     */
    public function __construct(array $config = null)
    {
        $this->client = new Client([
            'timeout' => 30,
            'http_errors' => false
        ]);
        
        $this->authService = new SeeruAuthService($config);
        
        if ($config) {
            $this->baseUrl = $config['api_base_url'] ?? config('services.seeru.endpoint');
        } else {
            $this->baseUrl = config('services.seeru.endpoint');
        }
    }
    
    /**
     * Check fare validity before booking
     * 
     * @param array $params Fare parameters
     * @return array|null
     */
    public function checkFare($params)
    {
        $token = $this->authService->getAuthToken();
        
        if (!$token) {
            Log::error('Seeru Check Fare Failed: No auth token');
            return null;
        }
        
        try {
            $response = $this->client->post($this->baseUrl . '/booking/fare', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $params,
            ]);
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            }
            
            Log::error('Seeru Check Fare Failed', [
                'status' => $response->getStatusCode(),
                'response' => (string)$response->getBody(),
                'params' => $params
            ]);
            return null;
            
        } catch (RequestException $e) {
            Log::error('Seeru Check Fare Exception: ' . $e->getMessage(), [
                'params' => $params
            ]);
            return null;
        }
    }
    
    /**
     * Save booking (hold or ready for issuance)
     * 
     * @param array $params Booking parameters
     * @return array|null
     */
    public function saveBooking($params)
    {
        $token = $this->authService->getAuthToken();
        
        if (!$token) {
            Log::error('Seeru Save Booking Failed: No auth token');
            return null;
        }
        
        try {
            $response = $this->client->post($this->baseUrl . '/booking/save', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $params,
            ]);
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            }
            
            Log::error('Seeru Save Booking Failed', [
                'status' => $response->getStatusCode(),
                'response' => (string)$response->getBody(),
                'params' => $params
            ]);
            return null;
            
        } catch (RequestException $e) {
            Log::error('Seeru Save Booking Exception: ' . $e->getMessage(), [
                'params' => $params
            ]);
            return null;
        }
    }
    
    /**
     * Issue ticket for an order
     * 
     * @param string $orderId Order ID
     * @return array|null
     */
    public function issueTicket($orderId)
    {
        $token = $this->authService->getAuthToken();
        
        if (!$token) {
            Log::error('Seeru Issue Ticket Failed: No auth token');
            return null;
        }
        
        try {
            $response = $this->client->post($this->baseUrl . '/order/issue', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => ['order_id' => $orderId],
            ]);
            
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return json_decode($response->getBody(), true);
            }
            
            Log::error('Seeru Issue Ticket Failed', [
                'status' => $response->getStatusCode(),
                'response' => (string)$response->getBody(),
                'orderId' => $orderId
            ]);
            return null;
            
        } catch (RequestException $e) {
            Log::error('Seeru Issue Ticket Exception: ' . $e->getMessage(), [
                'orderId' => $orderId
            ]);
            return null;
        }
    }
}
