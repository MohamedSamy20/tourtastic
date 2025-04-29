<?php

namespace App\Services\Flights;

interface FlightServiceInterface
{
    /**
     * Search for flights
     * 
     * @param array $params Search parameters
     * @return array|null
     */
    public function searchFlights($params);
    
    /**
     * Get search results by search ID
     * 
     * @param string $searchId Search ID
     * @return array|null
     */
    public function getSearchResult($searchId);
    
    /**
     * Check fare validity before booking
     * 
     * @param array $params Fare parameters
     * @return array|null
     */
    public function checkFare($params);
    
    /**
     * Save booking (hold or ready for issuance)
     * 
     * @param array $params Booking parameters
     * @return array|null
     */
    public function saveBooking($params);
    
    /**
     * Issue ticket for an order
     * 
     * @param string $orderId Order ID
     * @return array|null
     */
    public function issueTicket($orderId);
    
    /**
     * Cancel a booking
     * 
     * @param string $bookingId Booking ID
     * @return array|null
     */
    public function cancelBooking($bookingId);
    
    /**
     * Request refund for a ticket
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function requestRefund($ticketNumber);
    
    /**
     * Void a ticket
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function voidTicket($ticketNumber);
    
    /**
     * Retrieve ticket information
     * 
     * @param string $ticketNumber Ticket number
     * @return array|null
     */
    public function retrieveTicket($ticketNumber);
}
