<?php
namespace Modules\Flight\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\services\Flights\SeeruFlightSearchService;
use App\services\Flights\SeeruBookingService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Flight\Models\Flight;
use Modules\Flight\Models\FlightSeat;
use Modules\Flight\Models\Airline;
use Modules\Flight\Models\Airport;

class FlightController extends Controller
{
    protected $searchService;
    protected $bookingService;
    protected $amadeusBaseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $apiProvider;

    public function __construct()
    {
        // تحديد مزود API من الإعدادات
        $this->apiProvider = setting_item('flight_api_provider') ?? 'seeru';
        
        if ($this->apiProvider == 'seeru') {
            // استخدام Seeru API
            $this->searchService = new SeeruFlightSearchService();
            $this->bookingService = new SeeruBookingService();
        } else {
            // استخدام Amadeus API (الإعدادات الحالية)
            $this->amadeusBaseUrl = config('app.env') === 'production' 
                ? 'https://api.amadeus.com' 
                : 'https://test.api.amadeus.com';
                
            $this->clientId = env('AMADEUS_CLIENT_ID') ;
            $this->clientSecret = env('AMADEUS_CLIENT_SECRET');
        }
    }

    /**
     * Display flight search form
     */
    public function index(Request $request)
    {
        $data = [
            'rows' => Flight::search($request),
            'list_location' => Airport::getAll(),
            'flight_seat_type' => FlightSeat::getTypeOptions(),
            'airlines' => Airline::getAll(),
            'page_title' => setting_item_with_lang('flight_page_search_title'),
            'flight_min_price' => Flight::getMinPrice(),
            'flight_max_price' => Flight::getMaxPrice(),
        ];
        return view('Flight::frontend.index', $data);
    }

    /**
     * Handle flight search requests
     */
    public function search(Request $request)
    {
        if ($this->apiProvider == 'seeru') {
            return $this->searchWithSeeru($request);
        } else {
            return $this->searchWithAmadeus($request);
        }
    }
    
    /**
     * Handle flight search with Seeru API
     */
    private function searchWithSeeru(Request $request)
    {
        // بناء معلمات البحث
        $params = $this->buildSeeruSearchParams($request);

        // استدعاء API
        $searchResults = $this->searchService->searchFlights($params);
        
        if (!$searchResults || empty($searchResults['search_id'])) {
            Log::error('Seeru API Error: Failed to get search results', ['params' => $params]);
            return redirect()->back()->with('error', 'Flight search failed. Please try again.');
        }

        // الحصول على نتائج مفصلة باستخدام search_id
        $detailedResults = $this->searchService->getSearchResult($searchResults['search_id']);
        
        if (!$detailedResults || empty($detailedResults['flights'])) {
            Log::error('Seeru API Error: Failed to get detailed flight results', ['search_id' => $searchResults['search_id']]);
            return redirect()->back()->with('error', 'Flight search failed. Please try again.');
        }

        $flights = $this->processSeeruApiResponse($detailedResults);

        // تخزين search_id في الجلسة للاستخدام لاحقًا
        session(['seeru_search_id' => $searchResults['search_id']]);

        // إعداد البيانات للعرض
        $data = [
            'rows' => $flights,
            'search_params' => $request->all(),
            'seo_meta' => [
                'title' => 'Flight Search Results',
                'desc' => 'Find the best flight deals'
            ]
        ];

        return view('Flight::frontend.search', $data);
    }
    
    /**
     * Handle flight search with Amadeus API
     */
    private function searchWithAmadeus(Request $request)
    {
        // الحصول على رمز الوصول
        $accessToken = $this->getAmadeusAccessToken();
        if (!$accessToken) {
            return redirect()->back()->with('error', 'Could not connect to flight service');
        }

        // بناء معلمات API
        $params = $this->buildAmadeusSearchParams($request);

        // استدعاء API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($this->amadeusBaseUrl . '/v2/shopping/flight-offers', $params);

        // معالجة استجابة API
        if (!$response->successful()) {
            Log::error('Amadeus API Error: ' . $response->body());
            return redirect()->back()->with('error', 'Flight search failed. Please try again.');
        }

        $apiData = $response->json();
        $flights = $this->processAmadeusApiResponse($apiData);

        // إعداد البيانات للعرض
        $data = [
            'rows' => $flights,
            'search_params' => $request->all(),
            'seo_meta' => [
                'title' => 'Flight Search Results',
                'desc' => 'Find the best flight deals'
            ]
        ];

        return view('Flight::frontend.search', $data);
    }

    /**
     * Get Amadeus API access token
     */
    private function getAmadeusAccessToken()
    {
        try {
            $response = Http::asForm()->post($this->amadeusBaseUrl . '/v1/security/oauth2/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ]);

            return $response->json()['access_token'] ?? null;
            
        } catch (\Exception $e) {
            Log::error('Amadeus Auth Error: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * Build search parameters for Seeru API
     */
    private function buildSeeruSearchParams(Request $request)
    {
        $params = [
            'trips' => 'oneway',
            'adults' => $request->input('adults', 1),
            'children' => $request->input('children', 0),
            'infants' => $request->input('infants', 0),
            'cabin_class' => $request->input('seat_type', 'economy'),
            'direct_flights' => false
        ];

        // Origin and Destination
        if ($request->filled('from_where')) {
            $params['origin'] = $request->input('from_where');
        }
        if ($request->filled('to_where')) {
            $params['destination'] = $request->input('to_where');
        }

        // Date handling
        if ($request->filled('date')) {
            $dates = explode(' - ', $request->input('date'));
            $params['departure_date'] = Carbon::parse($dates[0])->format('Y-m-d');
            
            if (count($dates) > 1 && $params['trips'] == 'oneway') {
                $params['trips'] = 'return';
                $params['return_date'] = Carbon::parse($dates[1])->format('Y-m-d');
            }
        }

        return $params;
    }

    /**
     * Build search parameters for Amadeus API
     */
    private function buildAmadeusSearchParams(Request $request)
    {
        $params = [
            'currencyCode' => setting_item('currency_main') ?? 'USD',
            'adults' => $request->input('adults', 1)
        ];

        // Origin and Destination
        if ($request->filled('from_where')) {
            $params['originLocationCode'] = $request->input('from_where');
        }
        if ($request->filled('to_where')) {
            $params['destinationLocationCode'] = $request->input('to_where');
        }

        // Date handling
        if ($request->filled('date')) {
            $dates = explode(' - ', $request->input('date'));
            $params['departureDate'] = Carbon::parse($dates[0])->format('Y-m-d');
            if (count($dates) > 1) {
                $params['returnDate'] = Carbon::parse($dates[1])->format('Y-m-d');
            }
        }

        return $params;
    }

    /**
     * Process Seeru API response
     */
    private function processSeeruApiResponse($apiData)
    {
        $flights = collect();

        if (!empty($apiData['flights'])) {
            foreach ($apiData['flights'] as $flight) {
                $segments = $flight['segments'][0]; // Get first segment for oneway or outbound
                
                $flightObj = new \stdClass();
                $flightObj->id = $flight['flight_id'];
                $flightObj->origin = $segments[0]['departure_airport'];
                $flightObj->destination = $segments[count($segments) - 1]['arrival_airport'];
                $flightObj->departure_time = Carbon::parse($segments[0]['departure_time']);
                $flightObj->arrival_time = Carbon::parse($segments[count($segments) - 1]['arrival_time']);
                $flightObj->price = $flight['price']['total'];
                $flightObj->currency = $flight['price']['currency'];
                
                // Create airline object
                $airlineObj = new \stdClass();
                $airlineObj->name = $segments[0]['airline_name'];
                $airlineObj->code = $segments[0]['airline_code'];
                $flightObj->airline = $airlineObj;
                
                // Create airport objects
                $airportFromObj = new \stdClass();
                $airportFromObj->name = $segments[0]['departure_airport_name'];
                $airportFromObj->code = $segments[0]['departure_airport'];
                $flightObj->airportFrom = $airportFromObj;
                
                $airportToObj = new \stdClass();
                $airportToObj->name = $segments[count($segments) - 1]['arrival_airport_name'];
                $airportToObj->code = $segments[count($segments) - 1]['arrival_airport'];
                $flightObj->airportTo = $airportToObj;
                
                $flights->push($flightObj);
            }
        }

        return $flights;
    }

    /**
     * Process Amadeus API response
     */
    private function processAmadeusApiResponse($apiData)
    {
        $flights = collect();

        if (!empty($apiData['data'])) {
            foreach ($apiData['data'] as $offer) {
                $firstSegment = $offer['itineraries'][0]['segments'][0];
                $lastSegment = end($offer['itineraries'][0]['segments']);
                
                $flightObj = new \stdClass();
                $flightObj->id = $offer['id'];
                $flightObj->origin = $firstSegment['departure']['iataCode'];
                $flightObj->destination = $lastSegment['arrival']['iataCode'];
                $flightObj->departure_time = Carbon::parse($firstSegment['departure']['at']);
                $flightObj->arrival_time = Carbon::parse($lastSegment['arrival']['at']);
                $flightObj->price = $offer['price']['total'];
                $flightObj->currency = $offer['price']['currency'];
                
                // Create airline object
                $airlineObj = new \stdClass();
                $airlineObj->name = $firstSegment['carrierCode']; // Would need a mapping to actual airline names
                $airlineObj->code = $firstSegment['carrierCode'];
                $flightObj->airline = $airlineObj;
                
                // Create airport objects - would need a mapping to actual airport names
                $airportFromObj = new \stdClass();
                $airportFromObj->name = $firstSegment['departure']['iataCode'];
                $airportFromObj->code = $firstSegment['departure']['iataCode'];
                $flightObj->airportFrom = $airportFromObj;
                
                $airportToObj = new \stdClass();
                $airportToObj->name = $lastSegment['arrival']['iataCode'];
                $airportToObj->code = $lastSegment['arrival']['iataCode'];
                $flightObj->airportTo = $airportToObj;
                
                $flights->push($flightObj);
            }
        }

        return $flights;
    }

    /**
     * Get flight data for AJAX requests
     */
    public function getData($id, Request $request)
    {
        $flight = Flight::find($id);
        if (empty($flight)) {
            return $this->sendError(__("Flight not found"));
        }
        return $this->sendSuccess([
            'data' => $flight->dataForApi(),
        ]);
    }

    /**
     * Handle flight booking
     */
    public function booking(Request $request)
    {
        if ($this->apiProvider == 'seeru') {
            return $this->bookingWithSeeru($request);
        } else {
            // يمكن تنفيذ الحجز مع Amadeus هنا إذا لزم الأمر
            return redirect()->back()->with('error', 'Booking with Amadeus is not implemented yet');
        }
    }
    
    /**
     * Handle flight booking with Seeru
     */
    private function bookingWithSeeru(Request $request)
    {
        $searchId = session('seeru_search_id');
        $flightId = $request->input('flight_id');
        
        if (!$searchId || !$flightId) {
            return redirect()->back()->with('error', 'Invalid booking request');
        }
        
        // التحقق من صلاحية السعر
        $fareResult = $this->bookingService->checkFare([
            'search_id' => $searchId,
            'flight_id' => $flightId
        ]);
        
        if (!$fareResult || !isset($fareResult['valid']) || !$fareResult['valid']) {
            return redirect()->back()->with('error', 'Selected fare is no longer available');
        }
        
        // تخزين بيانات الحجز في الجلسة للدفع
        session([
            'seeru_booking_data' => [
                'search_id' => $searchId,
                'flight_id' => $flightId,
                'fare_data' => $fareResult
            ]
        ]);
        
        // التوجيه إلى صفحة معلومات الركاب
        return redirect()->route('flight.checkout');
    }

    /**
     * Handle flight checkout
     */
    public function checkout(Request $request)
    {
        if ($this->apiProvider != 'seeru') {
            return redirect()->route('flight.search')->with('error', 'Checkout is only available with Seeru API');
        }
        
        $bookingData = session('seeru_booking_data');
        
        if (!$bookingData) {
            return redirect()->route('flight.search')->with('error', 'No booking in progress');
        }
        
        // عرض نموذج الدفع
        return view('Flight::frontend.checkout', [
            'booking_data' => $bookingData
        ]);
    }

    /**
     * Complete booking
     */
    public function completeBooking(Request $request)
    {
        if ($this->apiProvider != 'seeru') {
            return redirect()->route('flight.search')->with('error', 'Booking completion is only available with Seeru API');
        }
        
        $bookingData = session('seeru_booking_data');
        
        if (!$bookingData) {
            return redirect()->route('flight.search')->with('error', 'No booking in progress');
        }
        
        // التحقق من صحة معلومات الركاب
        $request->validate([
            'passengers.*.type' => 'required',
            'passengers.*.title' => 'required',
            'passengers.*.first_name' => 'required',
            'passengers.*.last_name' => 'required',
            'passengers.*.gender' => 'required',
            'passengers.*.date_of_birth' => 'required|date',
            'contact.email' => 'required|email',
            'contact.phone' => 'required',
        ]);
        
        // إعداد معلمات الحجز
        $bookingParams = [
            'search_id' => $bookingData['search_id'],
            'flight_id' => $bookingData['flight_id'],
            'passengers' => $request->input('passengers'),
            'contact' => $request->input('contact')
        ];
        
        // حفظ الحجز
        $bookingResult = $this->bookingService->saveBooking($bookingParams);
        
        if (!$bookingResult || empty($bookingResult['booking_id'])) {
            return redirect()->back()->with('error', 'Booking failed. Please try again.');
        }
        
        // مسح بيانات الجلسة
        session()->forget('seeru_search_id');
        session()->forget('seeru_booking_data');
        
        // التوجيه إلى صفحة تأكيد الحجز
        return redirect()->route('flight.booking.confirmation', ['booking_id' => $bookingResult['booking_id']]);
    }

    /**
     * Display booking confirmation
     */
    public function bookingConfirmation($bookingId)
    {
        // هنا عادة ما تقوم بجلب تفاصيل الحجز من قاعدة البيانات الخاصة بك
        // لهذا المثال، سنعرض فقط معرف الحجز
        
        return view('Flight::frontend.booking-confirmation', [
            'booking_id' => $bookingId
        ]);
    }
}
