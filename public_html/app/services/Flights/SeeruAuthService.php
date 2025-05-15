<?php

namespace App\Services\Flights;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SeeruAuthService
{
    protected $apiKey;
    protected $refreshToken; // Keep for potential future use
    protected $baseUrl;

    public function __construct(array $config = null)
    {
        // Prioritize passed config, then Laravel config, then defaults
        $this->apiKey = $config['api_key'] ?? Config::get('services.seeru.api_key');
        $this->refreshToken = $config['refresh_key'] ?? Config::get('services.seeru.refresh_key');
        $this->baseUrl = $config['api_base_url'] ?? Config::get('services.seeru.endpoint'); // Use the same base URL logic

        if (!$this->apiKey) {
            Log::error('SeeruAuthService Error: Missing API key.');
            // Optionally throw an exception or handle the error appropriately
        }
         if (!$this->baseUrl) {
            Log::error('SeeruAuthService Error: Missing API base URL.');
             // Optionally throw an exception or handle the error appropriately
        }
    }

    /**
     * Get the authorization headers for Seeru API requests.
     * For now, it just uses the static API key.
     * Future implementation could include token refresh logic.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        if (!$this->apiKey) {
             Log::error('SeeruAuthService: Cannot get headers without an API key.');
             return []; // Return empty or throw exception
        }
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }

    /**
     * Placeholder for token refresh logic.
     * This would typically involve calling a refresh endpoint using the refreshToken.
     */
    protected function refreshTokenIfNeeded(): void
    {
        // Implementation for token refresh would go here.
        // Check if the current token is expired (if applicable)
        // Make a request to the refresh endpoint using $this->refreshToken
        // Update $this->apiKey with the new token
        // Potentially update the stored token (e.g., in cache or config)
        Log::info('SeeruAuthService: Token refresh logic not yet implemented.');
    }

    /**
     * Get the base URL for the API.
     *
     * @return string|null
     */
     public function getBaseUrl(): ?string
     {
         return $this->baseUrl;
     }
}

