<?php

namespace App\Services\Flights;

use Illuminate\Support\Facades\Http;
use App\Services\Flights\SeeruAuthService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SeeruFlightSearchService
{
    protected $authService;
    protected $baseUrl;

    // Use Dependency Injection for AuthService
    public function __construct(SeeruAuthService $authService, array $config = null) // Inject AuthService
    {
        $this->authService = $authService;
        $this->baseUrl = $this->authService->getBaseUrl();

        if (!$this->baseUrl) {
            Log::error("SeeruFlightSearchService Error: Missing API base URL.");
            throw new \Exception("SeeruFlightSearchService Error: Missing API base URL.");
        }
    }

    public function searchFlights(array $params)
    {
        // Extract parameters for the URL path, providing defaults
        $adults = $params["adults"] ?? 1;
        $children = $params["children"] ?? 0;
        $infants = $params["infants"] ?? 0;

        // --- Construct the 'trips' path parameter based on documentation --- 
        $tripSegments = [];
        $dateFormat = 'Ymd'; // Date format required by API path

        if (empty($params['origin']) || empty($params['destination']) || empty($params['departure_date'])) {
            throw new \InvalidArgumentException('Missing required search parameters: origin, destination, or departure_date.');
        }

        // Outbound trip
        $departureDate = Carbon::parse($params['departure_date'])->format($dateFormat);
        $tripSegments[] = "{$params['origin']}-{$params['destination']}-{$departureDate}";

        // Return trip (if return_date is provided)
        if (!empty($params['return_date'])) {
            $returnDate = Carbon::parse($params['return_date'])->format($dateFormat);
            // Assuming return trip is destination back to origin
            $tripSegments[] = "{$params['destination']}-{$params['origin']}-{$returnDate}";
        }
        
        $tripsPathSegment = implode(':', $tripSegments);
        // --- End: Construct 'trips' path parameter ---

        // Construct the endpoint path according to documentation
        $endpointPath = "/search/{$tripsPathSegment}/{$adults}/{$children}/{$infants}";

        // --- Prepare query parameters based on documentation --- 
        $queryParams = [];
        // 'cabin': Use 'cabin' key, map values if necessary (e.g., 'economy' -> 'e')
        if (!empty($params['cabin_class'])) { // Check original key from controller/request
            $cabinMap = ['economy' => 'e', 'premiumeconomy' => 'p', 'business' => 'b', 'first' => 'f'];
            $cabinKey = strtolower($params['cabin_class']);
            if (isset($cabinMap[$cabinKey])) {
                $queryParams['cabin'] = $cabinMap[$cabinKey];
            }
        }
        // 'direct': Use 'direct' key, ensure integer 0 or 1
        if (isset($params['direct_flights'])) { // Check original key from controller/request
             $queryParams['direct'] = filter_var($params['direct_flights'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }
        // --- End: Prepare query parameters ---

        $fullUrl = $this->baseUrl . $endpointPath;

        try {
            Log::info("Seeru Search Request", ["url" => $fullUrl, "params" => $queryParams]); // Log request details
            $response = Http::withHeaders($this->authService->getHeaders())
                ->get($fullUrl, $queryParams);

            if ($response->successful()) {
                Log::info("Seeru Search Success", ["status" => $response->status(), "response" => $response->json()]);
                return $response->json();
            }

            // Log the specific error from Seeru
            Log::error("Seeru Search Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "params" => $queryParams
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            // Catch Guzzle/HTTP exceptions or the one thrown above
            Log::error("Seeru Search Error Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "params" => $queryParams
            ]);
            throw new \Exception("Seeru Search Request Error: " . $e->getMessage());
        }
    }

    public function getSearchResult($searchId)
    {
        $fullUrl = $this->baseUrl . "/result/" . $searchId;
        try {
            Log::info("Seeru Get Result Request", ["url" => $fullUrl]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->get($fullUrl);

            if ($response->successful()) {
                 Log::info("Seeru Get Result Success", ["status" => $response->status()]);
                return $response->json();
            }

            Log::error("Seeru Get Search Result Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "searchId" => $searchId
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());
        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Get Search Result Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "searchId" => $searchId
            ]);
            throw new \Exception("Seeru Get Search Result Request Error: " . $e->getMessage());
        }
    }

    // Updated checkFare, saveBooking, issueTicket to use injected authService and consistent error handling

    public function checkFare(array $params)
    {
        $fullUrl = $this->baseUrl . "/booking/fare";
        try {
             Log::info("Seeru Check Fare Request", ["url" => $fullUrl, "params" => $params]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $params);

            if ($response->successful()) {
                 Log::info("Seeru Check Fare Success", ["status" => $response->status()]);
                return $response->json();
            }
            Log::error("Seeru Fare Check Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "params" => $params
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());
        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Fare Check Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "params" => $params
            ]);
            throw new \Exception("Seeru Fare Check Request Error: " . $e->getMessage());
        }
    }

    public function saveBooking(array $params)
    {
        $fullUrl = $this->baseUrl . "/booking/save";
        try {
            // Add date formatting for passengers if needed
            if (isset($params["passengers"])) {
                $params["passengers"] = array_map(function ($pax) {
                    if (!empty($pax["date_of_birth"])) {
                        $pax["date_of_birth"] = Carbon::parse($pax["date_of_birth"])->format("Y-m-d");
                    }
                    // Add formatting for passport expiry if needed
                    return $pax;
                }, $params["passengers"]);
            }
            Log::info("Seeru Save Booking Request", ["url" => $fullUrl, "params_keys" => array_keys($params)]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $params);

            if ($response->successful()) {
                 Log::info("Seeru Save Booking Success", ["status" => $response->status()]);
                return $response->json();
            }
            Log::error("Seeru Booking Save Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "params_keys" => array_keys($params)
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());
        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Booking Save Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
            ]);
            throw new \Exception("Seeru Booking Save Request Error: " . $e->getMessage());
        }
    }

    public function issueTicket(string $orderId)
    {
        $fullUrl = $this->baseUrl . "/order/issue";
        $payload = ["order_id" => $orderId];
        try {
            Log::info("Seeru Issue Ticket Request", ["url" => $fullUrl, "payload" => $payload]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                 Log::info("Seeru Issue Ticket Success", ["status" => $response->status()]);
                return $response->json();
            }
            Log::error("Seeru Ticket Issue Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());
        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Ticket Issue Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru Ticket Issue Request Error: " . $e->getMessage());
        }
    }//Add date formatting for passengers  and   Add formatting for passport expiry 

    //Add other methods (getOrderDetails, cancelOrder, etc.) similarly, using DI and consistent error handling

    public function getOrderDetails(string $orderId)
    {
        $fullUrl = $this->baseUrl . "/order/details";
        $payload = ["order_id" => $orderId];
        try {
            Log::info("Seeru Get Order Details Request", ["url" => $fullUrl, "payload" => $payload]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Get Order Details Success", ["status" => $response->status()]);
                return $response->json();
            }
            Log::error("Seeru Get Order Details Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());
        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Get Order Details Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru Get Order Details Request Error: " . $e->getMessage());
        }
    }

    public function cancelOrder(string $orderId)
    {
        $fullUrl = $this->baseUrl . "/order/cancel";
        $payload = ["order_id" => $orderId];
        try {
            Log::info("Seeru Cancel Order Request", ["url" => $fullUrl, "payload" => $payload]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Cancel Order Success", ["status" => $response->status()]);
                return $response->json();
            }
            Log::error("Seeru Cancel Order Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());
        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Cancel Order Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru Cancel Order Request Error: " . $e->getMessage());
        }
    }

    /**
     * Initiate ticket exchange (or calculate fees)
     * API: POST /ticket/exchange
     *
     * @param string $ticketId
     * @param array $exchangeLegs Array describing the legs to exchange and what to exchange them for.
     * @param array $passengersToExchange Optional array of passenger_ids to exchange.
     * @param float|null $totalFees If null, calculates fees. If set, executes exchange if fees match.
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
    public function exchangeTicket(string $ticketId, array $exchangeLegs = [], array $passengersToExchange = [], ?float $totalFees = null)
    {
        $fullUrl = $this->baseUrl . "/ticket/exchange";
        $payload = [
            "ticket_id" => $ticketId,
            "exchange_legs" => $exchangeLegs,
            "passengers" => $passengersToExchange,
            "total_fees" => $totalFees
        ];

        try {
            Log::info("Seeru Exchange Ticket Request", ["url" => $fullUrl, "payload_keys" => array_keys($payload)]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Exchange Ticket Success", ["status" => $response->status()]);
                return $response->json(); // Returns { "status": "success", "message": "string" } or fee details
            }

            Log::error("Seeru Exchange Ticket Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Exchange Ticket Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
            ]);
            throw new \Exception("Seeru Exchange Ticket Request Error: " . $e->getMessage());
        }
    }
}



