<?php

namespace App\Services\Flights;

use Illuminate\Support\Facades\Http;
use App\Services\Flights\SeeruAuthService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SeeruBookingService
{
    protected $authService;
    protected $baseUrl;

    // Use Dependency Injection for AuthService
    public function __construct(SeeruAuthService $authService, array $config = null)
    {
        $this->authService = $authService;
        $this->baseUrl = $this->authService->getBaseUrl();

        if (!$this->baseUrl) {
            Log::error("SeeruBookingService Error: Missing API base URL.");
            throw new \Exception("SeeruBookingService Error: Missing API base URL.");
        }
    }

    /**
     * Check fare validity before booking
     * API: POST /booking/fare
     *
     * @param array $bookingData The booking data structure from search results/fare check response.
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
    public function checkFare(array $bookingData)
    {
        $fullUrl = $this->baseUrl . "/booking/fare";
        // The API expects the booking data structure directly in the body, nested under a "booking" key.
        $payload = ["booking" => $bookingData];

        try {
            Log::info("Seeru Check Fare Request", ["url" => $fullUrl]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Check Fare Success", ["status" => $response->status()]);
                return $response->json(); // Returns { "status": "success", "booking": { ... updated booking ... } }
            }

            Log::error("Seeru Check Fare Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Check Fare Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
            ]);
            throw new \Exception("Seeru Check Fare Request Error: " . $e->getMessage());
        }
    }

    /**
     * Save booking (hold or ready for issuance)
     * API: POST /booking/save
     *
     * @param array $bookingData Booking data structure from fare check.
     * @param array $passengers Array of passenger details.
     * @param array $contact Contact details.
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
    public function saveBooking(array $bookingData, array $passengers, array $contact)
    {
        $fullUrl = $this->baseUrl . "/booking/save";
        $payload = [
            "booking" => $bookingData,
            "passengers" => $this->formatPassengersForSave($passengers),
            "contact" => $contact
        ];

        try {
            Log::info("Seeru Save Booking Request", ["url" => $fullUrl, "payload_keys" => array_keys($payload)]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Save Booking Success", ["status" => $response->status()]);
                return $response->json(); // Returns { "status": "success", "message": "string", "order_id": "string" }
            }

            Log::error("Seeru Save Booking Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Save Booking Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
            ]);
            throw new \Exception("Seeru Save Booking Request Error: " . $e->getMessage());
        }
    }

    /**
     * Format passenger data for the saveBooking request.
     */
    protected function formatPassengersForSave(array $passengers): array
    {
        return array_map(function ($pax) {
            // Ensure date formats are correct (Y-m-d)
            if (!empty($pax["birth_date"])) {
                try {
                    $pax["birth_date"] = Carbon::parse($pax["birth_date"])->format("Y-m-d");
                } catch (\Exception $e) {
                    Log::warning("Invalid birth_date format for passenger", ["pax" => $pax["pax_id"] ?? null]);
                    // Decide how to handle invalid date: nullify, throw error, etc.
                    // For now, let it pass, API might validate.
                }
            }
            if (!empty($pax["document_expiry"])) {
                 try {
                    $pax["document_expiry"] = Carbon::parse($pax["document_expiry"])->format("Y-m-d");
                } catch (\Exception $e) {
                    Log::warning("Invalid document_expiry format for passenger", ["pax" => $pax["pax_id"] ?? null]);
                }
            }
            // Ensure required fields are present (basic check)
            $required = ["pax_id", "type", "first_name", "last_name", "gender", "birth_date"];
            foreach ($required as $field) {
                if (empty($pax[$field])) {
                    Log::warning("Missing required passenger field", ["field" => $field, "pax" => $pax["pax_id"] ?? null]);
                    // Consider throwing an exception here for stricter validation
                }
            }
            return $pax;
        }, $passengers);
    }

    // --- Order Management --- 

    /**
     * Get order details
     * API: POST /order/details
     *
     * @param string $orderId
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
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

    /**
     * Issue ticket for a saved order
     * API: POST /order/issue
     *
     * @param string $orderId The Order ID obtained from saveBooking.
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
    public function issueTicket(string $orderId)
    {
        $fullUrl = $this->baseUrl . "/order/issue";
        // Documentation for /order/issue is missing, but assuming it takes order_id like /order/details
        // If it requires booking_id, this needs adjustment.
        $payload = ["order_id" => $orderId]; 

        try {
            Log::info("Seeru Issue Ticket Request", ["url" => $fullUrl, "payload" => $payload]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Issue Ticket Success", ["status" => $response->status()]);
                return $response->json(); // Expected response format unknown from docs
            }

            Log::error("Seeru Issue Ticket Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Issue Ticket Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru Issue Ticket Request Error: " . $e->getMessage());
        }
    }

    /**
     * Cancel a saved order
     * API: POST /order/cancel
     *
     * @param string $orderId
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
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
                return $response->json(); // Returns { "status": "success", "message": "string" }
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

    // --- Ticket Management --- 

    /**
     * Get ticket details by Ticket ID
     * API: POST /ticket/details
     *
     * @param string $ticketId
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
    public function getTicketDetails(string $ticketId)
    {
        $fullUrl = $this->baseUrl . "/ticket/details";
        $payload = ["ticket_id" => $ticketId];

        try {
            Log::info("Seeru Get Ticket Details Request", ["url" => $fullUrl, "payload" => $payload]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Get Ticket Details Success", ["status" => $response->status()]);
                return $response->json();
            }

            Log::error("Seeru Get Ticket Details Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Get Ticket Details Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru Get Ticket Details Request Error: " . $e->getMessage());
        }
    }

    /**
     * Retrieve ticket details by PNR and Last Name
     * API: POST /ticket/retrieve
     *
     * @param string $airlinePnr
     * @param string $lastName
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
    public function retrieveTicket(string $airlinePnr, string $lastName)
    {
        $fullUrl = $this->baseUrl . "/ticket/retrieve";
        $payload = [
            "airline_pnr" => $airlinePnr,
            "last_name" => $lastName
        ];

        try {
            Log::info("Seeru Retrieve Ticket Request", ["url" => $fullUrl, "payload" => $payload]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Retrieve Ticket Success", ["status" => $response->status()]);
                return $response->json();
            }

            Log::error("Seeru Retrieve Ticket Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Retrieve Ticket Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru Retrieve Ticket Request Error: " . $e->getMessage());
        }
    }

    /**
     * Initiate ticket refund (or calculate fees)
     * API: POST /ticket/refund
     *
     * @param string $ticketId
     * @param array $legsToRefund Optional array of leg_ids to refund.
     * @param array $passengersToRefund Optional array of passenger_ids to refund.
     * @param float|null $totalFees If null, calculates fees. If set, executes refund if fees match.
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
    public function refundTicket(string $ticketId, array $legsToRefund = [], array $passengersToRefund = [], ?float $totalFees = null)
    {
        $fullUrl = $this->baseUrl . "/ticket/refund";
        $payload = [
            "ticket_id" => $ticketId,
            "legs" => $legsToRefund, // API doc shows empty array, confirm if leg_ids needed
            "passengers" => $passengersToRefund,
            "total_fees" => $totalFees
        ];

        try {
            Log::info("Seeru Refund Ticket Request", ["url" => $fullUrl, "payload" => $payload]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Refund Ticket Success", ["status" => $response->status()]);
                return $response->json(); // Returns { "status": "success", "message": "string" } or fee details
            }

            Log::error("Seeru Refund Ticket Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Refund Ticket Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru Refund Ticket Request Error: " . $e->getMessage());
        }
    }

     /**
     * Initiate ticket void
     * API: POST /ticket/void
     *
     * @param string $ticketId
     * @param array $passengersToVoid Optional array of passenger_ids to void.
     * @return array API Response
     * @throws \Exception On API error or request failure.
     */
    public function voidTicket(string $ticketId, array $passengersToVoid = [])
    {
        $fullUrl = $this->baseUrl . "/ticket/void";
        $payload = [
            "ticket_id" => $ticketId,
            "passengers" => $passengersToVoid
        ];

        try {
            Log::info("Seeru Void Ticket Request", ["url" => $fullUrl, "payload" => $payload]);
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($fullUrl, $payload);

            if ($response->successful()) {
                Log::info("Seeru Void Ticket Success", ["status" => $response->status()]);
                return $response->json(); // Returns { "status": "success", "message": "string" }
            }

            Log::error("Seeru Void Ticket Failed", [
                "status" => $response->status(),
                "response" => $response->body(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru API Error ({$response->status()}): " . $response->body());

        } catch (\Illuminate\Http\Client\RequestException | \Exception $e) {
            Log::error("Seeru Void Ticket Exception", [
                "message" => $e->getMessage(),
                "url" => $fullUrl,
                "payload" => $payload
            ]);
            throw new \Exception("Seeru Void Ticket Request Error: " . $e->getMessage());
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
    public function exchangeTicket(string $ticketId, array $exchangeLegs, array $passengersToExchange = [], ?float $totalFees = null)
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

    // --- Webhook --- 
    // The documentation mentions webhooks but doesn't provide an endpoint to configure them.
    // Configuration might be done via the Seeru portal.
    // We would need a controller method to *receive* webhook POST requests if configured.
}

