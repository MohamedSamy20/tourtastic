<div class="card">
    <div class="card-header bg-success text-white">
        Booking Confirmed!
    </div>
    <div class="card-body">
        <h5 class="card-title">Thank you for your booking.</h5>
        <p class="card-text">Your flight booking has been successfully processed.</p>
        <div id="confirmation_details">
            {{-- JavaScript will populate this section --}}
            {{-- Example: <p><strong>Order ID:</strong> <span id="conf_order_id"></span></p> --}}
            {{-- Example: <p><strong>PNR:</strong> <span id="conf_pnr"></span></p> --}}
        </div>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Back to Home</a>
        {{-- You might want a link to a "My Bookings" page if it exists --}}
    </div>
</div>

