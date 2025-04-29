<?php

namespace App\Services\Flights;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SeeruAuthService
{
    protected $config;
    protected $token;

    public function __construct(array $config = null)
    {
        $this->config = $config ?? config('services.seeru');
    }

    public function getToken()
    {
        return Cache::remember('seeru_jwt_token', 50, function () {
            $response = Http::post($this->config['endpoint'].'/auth/login', [
                'email' => $this->config['email'],
                'password' => $this->config['password'],
                'agency_code' => $this->config['agency_code'],
            ]);

            if ($response->successful()) {
                return $response->json('token');
            }

            throw new \Exception('Seeru Auth Failed: ' . $response->body());
        });
    }

    public function getHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }
}
