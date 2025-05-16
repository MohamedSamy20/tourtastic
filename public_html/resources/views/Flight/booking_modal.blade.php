<!-- Booking Modal -->
<div class="modal fade" id="flightBookingModal" tabindex="-1" aria-labelledby="flightBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flightBookingModalLabel">Enter Passenger Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Confirmed flight details will be shown here (optional) -->
                <div id="modalFlightSummary" class="mb-3">
                    {{-- Example: <p><strong>Flight:</strong> <span id="modalFlightDetails"></span></p> --}}
                    {{-- Example: <p><strong>Price:</strong> <span id="modalFlightPrice"></span></p> --}}
                </div>

                <form id="passengerDetailsForm">
                    {{-- Hidden input to store confirmed flight data from fare check --}}
                    {{-- <input type="hidden" name="confirmed_flight_data" value=\""> --}}

                    <h5>Lead Passenger</h5>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="passenger_title_1">Title</label>
                            <select id="passenger_title_1" name="passenger_title_1" class="form-control" required>
                                <option value="Mr">Mr</option>
                                <option value="Ms">Ms</option>
                                <option value="Mrs">Mrs</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="passenger_firstname_1">First Name</label>
                            <input type="text" class="form-control" id="passenger_firstname_1" name="passenger_firstname_1" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="passenger_lastname_1">Last Name</label>
                            <input type="text" class="form-control" id="passenger_lastname_1" name="passenger_lastname_1" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="passenger_dob_1">Date of Birth</label>
                            <input type="date" class="form-control" id="passenger_dob_1" name="passenger_dob_1" required>
                        </div>
                    </div>
                    
                    {{-- Add more passenger fields/sections here if needed --}}
                    {{-- e.g., for multiple adults, children, infants --}}

                    <hr>
                    <h5>Contact Details</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="contact_email">Email Address</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="contact_phone">Phone Number</label>
                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" required>
                        </div>
                    </div>

                    <div id="bookingModalMessages" class="mt-3"></div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

