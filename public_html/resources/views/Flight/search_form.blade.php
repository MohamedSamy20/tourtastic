<form id="flightSearchForm" class="bravo-form-search-flight" method="GET" action="{{ route("flight.search") }}">
    @csrf {{-- Although GET, good practice if it ever becomes POST or for consistency --}}
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="origin">Origin</label>
            <input type="text" class="form-control" id="origin" name="origin" placeholder="City or airport code (e.g., DXB)" required>
        </div>
        <div class="form-group col-md-3">
            <label for="destination">Destination</label>
            <input type="text" class="form-control" id="destination" name="destination" placeholder="City or airport code (e.g., LHR)" required>
        </div>
        <div class="form-group col-md-2">
            <label for="departure_date">Departure Date</label>
            <input type="date" class="form-control" id="departure_date" name="departure_date" required>
        </div>
        <div class="form-group col-md-2">
            <label for="return_date">Return Date</label>
            <input type="date" class="form-control" id="return_date" name="return_date">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-1">
            <label for="adults">Adults</label>
            <input type="number" class="form-control" id="adults" name="adults" value="1" min="1">
        </div>
        <div class="form-group col-md-1">
            <label for="children">Children</label>
            <input type="number" class="form-control" id="children" name="children" value="0" min="0">
        </div>
        <div class="form-group col-md-1">
            <label for="infants">Infants</label>
            <input type="number" class="form-control" id="infants" name="infants" value="0" min="0">
        </div>
        <div class="form-group col-md-2">
            <label for="cabin_class">Cabin Class</label>
            <select id="cabin_class" name="cabin_class" class="form-control">
                <option value="economy" selected>Economy</option>
                <option value="premium_economy">Premium Economy</option>
                <option value="business">Business</option>
                <option value="first">First</option>
            </select>
        </div>
        <div class="form-group col-md-2 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="direct_flights" name="direct_flights" value="true">
                <label class="form-check-label" for="direct_flights">
                    Direct Flights Only
                </label>
            </div>
        </div>
        <div class="form-group col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-block">Search Flights</button>
        </div>
    </div>
</form>

