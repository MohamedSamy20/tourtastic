<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AmadeusService
{
    protected $client;
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 15, // Add timeout
            'http_errors' => false // Handle errors manually
        ]);
        
        // Use standard Amadeus environment variables
        $this->baseUrl = env('AMADEUS_ENV') === 'production' 
            ? 'https://api.amadeus.com' 
            : 'https://test.api.amadeus.com';
            
        $this->clientId = env('AMADEUS_CLIENT_ID');
        $this->clientSecret = env('AMADEUS_CLIENT_SECRET');
    }

    public function getAccessToken()
    {
        return Cache::remember('amadeus_access_token', 1700, function () { // 1700 seconds (28.3 mins)
            try {
                $response = $this->client->post($this->baseUrl . '/v1/security/oauth2/token', [
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                    ],
                ]);

                if ($response->getStatusCode() === 200) {
                    $data = json_decode($response->getBody(), true);
                    return $data['access_token'] ?? null;
                }

                Log::error('Amadeus Auth Failed', [
                    'status' => $response->getStatusCode(),
                    'response' => (string)$response->getBody()
                ]);
                return null;

            } catch (\Exception $e) {
                Log::error('Amadeus Auth Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function searchFlights(array $params)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error('Amadeus Search Failed: No access token');
                return null;
            }

            $response = $this->client->get($this->baseUrl . '/v2/shopping/flight-offers', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
                'query' => $this->normalizeParams($params),
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true);
            }

            Log::error('Amadeus Search Failed', [
                'status' => $response->getStatusCode(),
                'response' => (string)$response->getBody(),
                'params' => $params
            ]);
            return null;

        } catch (RequestException $e) {
            Log::error('Amadeus Search Exception: ' . $e->getMessage());
            return null;
        }
    }

    private function normalizeParams(array $params): array
    {
        // Convert date format to YYYY-MM-DD
        if (isset($params['departureDate'])) {
            $params['departureDate'] = Carbon::parse($params['departureDate'])->format('Y-m-d');
        }
        if (isset($params['returnDate'])) {
            $params['returnDate'] = Carbon::parse($params['returnDate'])->format('Y-m-d');
        }

        // Remove empty parameters
        return array_filter($params, function ($value) {
            return !empty($value);
        });
    }
}