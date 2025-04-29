@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ __('Available Flights') }}</h1>
    
    @if($rows->count() > 0)
        <div class="row">
            @foreach($rows as $flight)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $flight->title }}</h5>
                            <p class="card-text">
                                {{ $flight->airportFrom->name ?? 'Unknown Origin' }} →
                                {{ $flight->airportTo->name ?? 'Unknown Destination' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    @else
        <div class="alert alert-warning">
            {{ __('No flights available at the moment.') }}
        </div>
    @endif
</div>
@endsection