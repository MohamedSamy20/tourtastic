{{-- resources/views/frontend/search.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Flight Search Results</h1>

        {{-- Show search parameters --}}
        @if(!empty($search_params))
            <div class="alert alert-info">
                Search Criteria:
                <ul>
                    <li>From: {{ $search_params['originLocationCode'] ?? 'Any' }}</li>
                    <li>To: {{ $search_params['destinationLocationCode'] ?? 'Any' }}</li>
                    <li>Departure: {{ $search_params['departureDate'] ?? 'Any date' }}</li>
                    @if(isset($search_params['returnDate']))
                    <li>Return: {{ $search_params['returnDate'] }}</li>
                    @endif
                </ul>
            </div>
        @endif

        {{-- Flight List --}}
        @if(count($rows) > 0)
            <div class="flight-list">
                @foreach($rows as $flight)
                    <div class="flight-item card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <img src="https://content.airhex.com/content/logos/airlines_{{ $flight['airline'] }}_50_50_r.png" 
                                         alt="{{ $flight['airline'] }} logo"
                                         class="airline-logo">
                                    <div class="mt-2">
                                        {{ $flight['airline'] }} {{ $flight['flight_number'] }}
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="flight-time">
                                                <div class="text-primary">
                                                    {{ \Carbon\Carbon::parse($flight['departure'])->format('H:i') }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $flight['origin'] }} • 
                                                    {{ \Carbon\Carbon::parse($flight['departure'])->format('D, d M') }}
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-6">
                                            <div class="flight-time">
                                                <div class="text-primary">
                                                    {{ \Carbon\Carbon::parse($flight['arrival'])->format('H:i') }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $flight['destination'] }} • 
                                                    {{ \Carbon\Carbon::parse($flight['arrival'])->format('D, d M') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 text-muted small">
                                        <i class="fas fa-clock"></i> 
                                        Duration: {{ $flight['duration'] }}
                                    </div>
                                </div>
                                
                                <div class="col-md-4 text-end">
                                    <div class="price-display">
                                        <div class="h4 text-success">
                                            {{ $flight['currency'] }} {{ number_format($flight['price'], 2) }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $flight['seats_available'] }} seats remaining
                                        </small>
                                    </div>
                                    <button class="btn btn-primary mt-2">
                                        <i class="fas fa-shopping-cart"></i> Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-warning">
                No flights found matching your criteria.
                <a href="{{ route('flight.index') }}" class="alert-link">Try a new search</a>
            </div>
        @endif
    </div>
@endsection

@section('css')
<style>
.airline-logo {
    max-width: 50px;
    max-height: 50px;
    object-fit: contain;
}
.flight-item {
    transition: transform 0.2s;
}
.flight-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.price-display {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}
</style>
@endsection