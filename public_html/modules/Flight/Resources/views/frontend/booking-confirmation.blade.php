@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="m-0">{{ __('Booking Confirmed!') }}</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fa fa-check-circle text-success" style="font-size: 64px;"></i>
                    </div>
                    
                    <h4>{{ __('Thank you for your booking') }}</h4>
                    <p>{{ __('Your booking has been confirmed. Your booking reference number is:') }}</p>
                    
                    <div class="alert alert-info">
                        <h3 class="text-center">{{ $booking_id }}</h3>
                    </div>
                    
                    <p>{{ __('A confirmation email has been sent to your email address with all the details of your booking.') }}</p>
                    
                    <p>{{ __('If you have any questions or need assistance, please contact our customer support.') }}</p>
                    
                    <div class="text-center mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary">{{ __('Return to Home') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
