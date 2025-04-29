@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ __('Flight Search Results') }}</h1>
    
    @if($rows->count() > 0)
        <div class="row">
            @foreach($rows as $flight)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $flight->airportFrom->name ?? 'Unknown Origin' }} →
                                {{ $flight->airportTo->name ?? 'Unknown Destination' }}
                            </h5>
                            <p class="card-text">
                                @if($flight->airline)
                                    {{ $flight->airline->name }}<br>
                                @endif
                                Departure: {{ $flight->departure_time->format('M d, Y H:i') }}<br>
                                Arrival: {{ $flight->arrival_time->format('M d, Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $rows->appends(request()->query())->links() }}
        </div>
    @else
        <div class="alert alert-warning">
            {{ __('No flights found matching your criteria.') }}
        </div>
    @endif
</div>
@endsection