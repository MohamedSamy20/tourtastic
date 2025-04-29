@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ __('Flight Checkout') }}</h1>
    
    @if(isset($booking_data))
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        {{ __('Passenger Information') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('flight.complete_booking') }}" method="POST">
                            @csrf
                            
                            <div class="passenger-form" id="passenger-form">
                                <h5>{{ __('Passenger 1 (Adult)') }}</h5>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label>{{ __('Title') }}</label>
                                        <select name="passengers[0][title]" class="form-control" required>
                                            <option value="MR">{{ __('Mr') }}</option>
                                            <option value="MRS">{{ __('Mrs') }}</option>
                                            <option value="MS">{{ __('Ms') }}</option>
                                        </select>
                                        <input type="hidden" name="passengers[0][type]" value="ADT">
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>{{ __('First Name') }}</label>
                                        <input type="text" name="passengers[0][first_name]" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>{{ __('Last Name') }}</label>
                                        <input type="text" name="passengers[0][last_name]" class="form-control" required>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>{{ __('Gender') }}</label>
                                        <select name="passengers[0][gender]" class="form-control" required>
                                            <option value="M">{{ __('Male') }}</option>
                                            <option value="F">{{ __('Female') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('Date of Birth') }}</label>
                                        <input type="date" name="passengers[0][date_of_birth]" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>{{ __('Nationality') }}</label>
                                        <input type="text" name="passengers[0][nationality]" class="form-control" value="SA">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>{{ __('Passport Number') }}</label>
                                        <input type="text" name="passengers[0][passport_number]" class="form-control">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ __('Passport Expiry') }}</label>
                                        <input type="date" name="passengers[0][passport_expiry]" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h5>{{ __('Contact Information') }}</h5>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>{{ __('Email') }}</label>
                                    <input type="email" name="contact[email]" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>{{ __('Phone') }}</label>
                                    <input type="text" name="contact[phone]" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>{{ __('Address') }}</label>
                                <input type="text" name="contact[address]" class="form-control">
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-4">
                                {{ __('Complete Booking') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        {{ __('Booking Summary') }}
                    </div>
                    <div class="card-body">
                        @if(isset($booking_data['fare_data']['flight']))
                            <h5>{{ $booking_data['fare_data']['flight']['segments'][0][0]['departure_airport'] }} → 
                                {{ $booking_data['fare_data']['flight']['segments'][0][count($booking_data['fare_data']['flight']['segments'][0])-1]['arrival_airport'] }}</h5>
                            <p>
                                {{ $booking_data['fare_data']['flight']['segments'][0][0]['airline_name'] }}<br>
                                {{ __('Departure') }}: {{ \Carbon\Carbon::parse($booking_data['fare_data']['flight']['segments'][0][0]['departure_time'])->format('M d, Y H:i') }}<br>
                                {{ __('Arrival') }}: {{ \Carbon\Carbon::parse($booking_data['fare_data']['flight']['segments'][0][count($booking_data['fare_data']['flight']['segments'][0])-1]['arrival_time'])->format('M d, Y H:i') }}
                            </p>
                            
                            <hr>
                            
                            <h5>{{ __('Price Details') }}</h5>
                            <p>
                                {{ __('Base Fare') }}: {{ $booking_data['fare_data']['flight']['price']['currency'] }} {{ $booking_data['fare_data']['flight']['price']['base'] }}<br>
                                {{ __('Taxes & Fees') }}: {{ $booking_data['fare_data']['flight']['price']['currency'] }} {{ $booking_data['fare_data']['flight']['price']['tax'] }}<br>
                                <strong>{{ __('Total') }}: {{ $booking_data['fare_data']['flight']['price']['currency'] }} {{ $booking_data['fare_data']['flight']['price']['total'] }}</strong>
                            </p>
                        @else
                            <p>{{ __('Booking information not available') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            {{ __('No booking in progress. Please search for flights first.') }}
            <a href="{{ route('flight.search') }}" class="btn btn-primary">{{ __('Search Flights') }}</a>
        </div>
    @endif
</div>
@endsection
