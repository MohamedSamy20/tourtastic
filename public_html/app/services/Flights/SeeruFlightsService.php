<?php

namespace App\Services\Flights;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class SeeruFlightsService
{
    protected $client;
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'http_errors' => false,
        ]);

        $this->baseUrl = rtrim(Config::get('services.seeru.endpoint', 'https://sandbox-api.seeru.travel/v1/flights'), '/');
        $this->apiKey = Config::get('services.seeru.api_key');

        if (!$this->apiKey) {
            Log::error('Seeru Service Error: Missing API key in config/services.php or .env');
        }
    }

    protected function makeRequest(string $method, string $endpoint, array $options = []): ?array
    {
        $defaultHeaders = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $options['headers'] = array_merge($defaultHeaders, $options['headers'] ?? []);

        try {
            $response = $this->client->request($method, $this->baseUrl . $endpoint, $options);
            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents(), true);

            if ($statusCode >= 200 && $statusCode < 300) {
                return $body;
            } else {
                Log::error('Seeru API Error', [
                    'endpoint' => $endpoint,
                    'status' => $statusCode,
                    'options' => $options,
                    'response' => $body ?? (string)$response->getBody()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Seeru API Exception: {$e->getMessage()}", ['endpoint' => $endpoint]);
            return null;
        }
    }

    public function searchFlights(array $params): ?array
    {
        $tripType = $params['trips'] ?? 'oneway';
        $adults = $params['adults'] ?? 1;
        $children = $params['children'] ?? 0;
        $infants = $params['infants'] ?? 0;

        $endpoint = "/search/{$tripType}/{$adults}/{$children}/{$infants}";
        $query = $this->prepareSearchQuery($params);

        return $this->makeRequest('GET', $endpoint, ['query' => $query]);
    }

    protected function prepareSearchQuery(array $params): array
    {
        $query = [];

        if (!empty($params['origin'])) $query['origin'] = $params['origin'];
        if (!empty($params['destination'])) $query['destination'] = $params['destination'];
        if (!empty($params['departure_date'])) $query['departure_date'] = Carbon::parse($params['departure_date'])->format('Y-m-d');
        if (!empty($params['return_date'])) $query['return_date'] = Carbon::parse($params['return_date'])->format('Y-m-d');
        if (!empty($params['cabin_class'])) $query['cabin_class'] = $params['cabin_class'];
        if (isset($params['direct_flights'])) $query['direct_flights'] = filter_var($params['direct_flights'], FILTER_VALIDATE_BOOLEAN);

        return array_filter($query);
    }

    public function getSearchResult(string $searchId): ?array
    {
        if (empty($searchId)) {
            Log::error('Seeru getSearchResult Error: searchId is empty.');
            return null;
        }

        return $this->makeRequest('GET', "/result/{$searchId}");
    }

    public function checkFare(array $params): ?array
    {
        if (empty($params['search_id']) || empty($params['flight_id'])) {
            Log::error('Seeru checkFare Error: Missing search_id or flight_id.');
            return null;
        }

        $body = [
            'search_id' => $params['search_id'],
            'flight_id' => $params['flight_id'],
        ];

        return $this->makeRequest('POST', '/booking/fare', ['json' => $body]);
    }

    public function saveBooking(array $params): ?array
    {
        if (empty($params['search_id']) || empty($params['flight_id']) || empty($params['passengers']) || empty($params['contact'])) {
            Log::error('Seeru saveBooking Error: Missing required parameters.');
            return null;
        }

        $passengers = array_map(function ($pax) {
            if (!empty($pax['date_of_birth'])) {
                $pax['date_of_birth'] = Carbon::parse($pax['date_of_birth'])->format('Y-m-d');
            }
            return $pax;
        }, $params['passengers']);

        $body = [
            'search_id' => $params['search_id'],
            'flight_id' => $params['flight_id'],
            'passengers' => $passengers,
            'contact' => $params['contact'],
        ];

        return $this->makeRequest('POST', '/booking/save', ['json' => $body]);
    }

    public function issueTicket(string $orderId): ?array
    {
        if (empty($orderId)) {
            Log::error('Seeru issueTicket Error: Missing orderId.');
            return null;
        }

        return $this->makeRequest('POST', '/order/issue', ['json' => ['booking_id' => $orderId]]);
    }

    public function cancelBooking(string $orderId): ?array
    {
        Log::warning('Seeru cancelBooking method not implemented.');
        return null;
    }

    public function requestRefund(string $orderId, array $details): ?array
    {
        Log::warning('Seeru requestRefund method not implemented.');
        return null;
    }

    public function voidTicket(string $orderId): ?array
    {
        Log::warning('Seeru voidTicket method not implemented.');
        return null;
    }

    public function retrieveTicket(string $orderId): ?array
    {
        Log::warning('Seeru retrieveTicket method not implemented.');
        return null;
    }
}
