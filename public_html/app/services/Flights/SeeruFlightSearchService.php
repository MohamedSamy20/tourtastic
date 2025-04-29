<?php

namespace App\Services\Flights;

use Illuminate\Support\Facades\Http;
use App\Services\Flights\SeeruAuthService;

class SeeruFlightSearchService
{
    protected $authService;
    protected $baseUrl;

    public function __construct(array $config = null)
    {
        $this->authService = new SeeruAuthService($config);
        $this->baseUrl = $config['api_base_url'] ?? config('services.flight.endpoint');
    }

    public function searchFlights(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->get($this->baseUrl . '/search', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Search Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Search Error: ' . $e->getMessage());
        }
    }

    public function getSearchResult($searchId)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->get($this->baseUrl . '/result/' . $searchId);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Search Result Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Search Result Error: ' . $e->getMessage());
        }
    }

    public function checkFare(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/booking/fare', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Fare Check Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Fare Check Error: ' . $e->getMessage());
        }
    }

    public function saveBooking(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/booking/save', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Booking Save Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Booking Save Error: ' . $e->getMessage());
        }
    }

    public function issueTicket($orderId)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/order/issue', ['order_id' => $orderId]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Ticket Issue Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Ticket Issue Error: ' . $e->getMessage());
        }
    }

    public function getOrderDetails(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/order/details', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Order Details Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Order Details Error: ' . $e->getMessage());
        }
    }

    public function cancelOrder(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/order/cancel', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Order Cancel Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Order Cancel Error: ' . $e->getMessage());
        }
    }

    public function getTicketDetails(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/ticket/details', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Ticket Details Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Ticket Details Error: ' . $e->getMessage());
        }
    }

    public function retrieveTicket(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/ticket/retrieve', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Ticket Retrieve Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Ticket Retrieve Error: ' . $e->getMessage());
        }
    }

    public function refundTicket(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/ticket/refund', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Ticket Refund Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Ticket Refund Error: ' . $e->getMessage());
        }
    }

    public function voidTicket(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/ticket/void', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Ticket Void Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Ticket Void Error: ' . $e->getMessage());
        }
    }

    public function exchangeTicket(array $params)
    {
        try {
            $response = Http::withHeaders($this->authService->getHeaders())
                ->post($this->baseUrl . '/ticket/exchange', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Seeru Ticket Exchange Failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Seeru Ticket Exchange Error: ' . $e->getMessage());
        }
    }
}
