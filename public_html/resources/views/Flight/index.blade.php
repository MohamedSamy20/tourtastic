@extends("layouts.app")

@section("content")
<div class="container bravo-flight-page">
    {{-- Breadcrumbs or page title can go here --}}
    <div class="row">
        <div class="col-md-12">
            <h1>Flight Search</h1>
            <p>Find the best flights for your next journey.</p>
        </div>
    </div>

    <div id="flight-search-container" class="my-4">
        @include("flights.partials.search_form")
    </div>

    <div id="flight-results-container" class="my-4">
        {{-- Flight results will be dynamically injected here by JavaScript --}}
        <div id="flight-results-loader" style="display: none; text-align: center;">
            <p>Loading flight results...</p>
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div id="flight-results"></div>
    </div>

    <div id="booking-modal-container">
        {{-- Booking modal will be included or dynamically built here --}}
        @include("flights.partials.booking_modal")
    </div>

    <div id="booking-confirmation-container" class="my-4" style="display: none;">
        {{-- Booking confirmation will be dynamically injected here --}}
        <div id="booking-confirmation"></div>
    </div>
</div>
@endsection

@push("js")
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const flightSearchForm = document.getElementById("flightSearchForm");
    const flightResultsDiv = document.getElementById("flight-results");
    const flightResultsLoader = document.getElementById("flight-results-loader");
    const bookingConfirmationContainer = document.getElementById("booking-confirmation-container");
    const bookingConfirmationDiv = document.getElementById("booking-confirmation");

    // --- CSRF Token for Axios ---
    const csrfToken = document.querySelector("meta[name=\"csrf-token\"]")?.getAttribute("content");
    if (csrfToken) {
        axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
    }

    // --- Function to display error messages ---
    function displayError(container, message) {
        container.innerHTML = `<div class="alert alert-danger">${message}</div>`;
    }

    // --- Function to display success messages ---
    function displaySuccess(container, message) {
        container.innerHTML = `<div class="alert alert-success">${message}</div>`;
        container.style.display = "block";
    }

    // --- Handle Flight Search Form Submission ---
    if (flightSearchForm) {
        flightSearchForm.addEventListener("submit", function(event) {
            event.preventDefault();
            flightResultsLoader.style.display = "block";
            flightResultsDiv.innerHTML = "";
            bookingConfirmationContainer.style.display = "none";
            bookingConfirmationDiv.innerHTML = "";

            const formData = new FormData(flightSearchForm);
            const params = new URLSearchParams(formData);

            axios.get(`{{ route("flight.search") }}?${params.toString()}`)
                .then(function(response) {
                    flightResultsLoader.style.display = "none";
                    if (response.data && response.data.results && response.data.results.data && response.data.results.data.length > 0) {
                        renderFlightResults(response.data.results.data, response.data.search_id);
                    } else if (response.data && response.data.results && response.data.results.message) {
                        flightResultsDiv.innerHTML = `<div class="alert alert-info">${response.data.results.message}</div>`;
                    } else {
                        flightResultsDiv.innerHTML = `<div class="alert alert-info">No flights found matching your criteria.</div>`;
                    }
                })
                .catch(function(error) {
                    flightResultsLoader.style.display = "none";
                    let errorMessage = "An error occurred while searching for flights.";
                    if (error.response && error.response.data && error.response.data.error) {
                        errorMessage = error.response.data.error;
                        if (error.response.data.message) errorMessage += ": " + error.response.data.message;
                    }
                    displayError(flightResultsDiv, errorMessage);
                    console.error("Flight search error:", error);
                });
        });
    }

    // --- Function to Render Flight Results ---
    function renderFlightResults(flights, searchId) {
        let html = `<table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Flight Details</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>`;
        flights.forEach(flight => {
            const firstSegment = flight.segments?.[0];
            const lastSegment = flight.segments?.[flight.segments.length - 1];
            const currency = flight.price?.currency_code || "USD";
            const amount = flight.price?.total_amount || "N/A";

            html += `
                <tr>
                    <td>
                        <strong>${firstSegment?.marketing_airline_code || ""} ${firstSegment?.flight_number || ""}</strong><br>
                        ${firstSegment?.departure_airport_code || "N/A"} (${firstSegment?.departure_datetime || "N/A"}) 
                        &rarr; 
                        ${lastSegment?.arrival_airport_code || "N/A"} (${lastSegment?.arrival_datetime || "N/A"})<br>
                        Duration: ${flight.total_duration || "N/A"}<br>
                        Stops: ${flight.segments ? flight.segments.length - 1 : "N/A"}
                    </td>
                    <td>
                        <strong>${amount} ${currency}</strong>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm book-now-btn" 
                                data-flight='${JSON.stringify(flight)}' 
                                data-search-id="${searchId}">
                            Book Now
                        </button>
                    </td>
                </tr>`;
        });
        html += "</tbody></table>";
        flightResultsDiv.innerHTML = html;

        // Add event listeners for the new "Book Now" buttons
        document.querySelectorAll(".book-now-btn").forEach(button => {
            button.addEventListener("click", handleBookNowClick);
        });
    }

    // --- Handle "Book Now" Click (Triggers Fare Check) ---
    function handleBookNowClick(event) {
        const flightData = JSON.parse(event.target.getAttribute("data-flight"));
        const searchId = event.target.getAttribute("data-search-id");
        const fareCheckPayload = {
            booking: flightData
        };

        event.target.disabled = true;
        event.target.innerHTML = "Checking Fare...";

        axios.post(`{{ route("flight.fare.check") }}`, fareCheckPayload)
            .then(function(response) {
                event.target.disabled = false;
                event.target.innerHTML = "Book Now";
                if (response.data && response.data.results) {
                    openBookingModal(response.data.results);
                } else {
                    displayError(flightResultsDiv, "Fare check succeeded but no data returned.");
                }
            })
            .catch(function(error) {
                event.target.disabled = false;
                event.target.innerHTML = "Book Now";
                let errorMessage = "An error occurred during fare check.";
                if (error.response && error.response.data && error.response.data.error) {
                    errorMessage = error.response.data.error;
                    if (error.response.data.message) errorMessage += ": " + error.response.data.message;
                }
                const errorCell = event.target.closest("td");
                if (errorCell) displayError(errorCell, errorMessage);
                else displayError(flightResultsDiv, errorMessage);
                console.error("Fare check error:", error);
            });
    }

    // --- Function to Open Booking Modal (and populate it) ---
    function openBookingModal(confirmedFlightData) {
        const bookingModal = new bootstrap.Modal(document.getElementById("flightBookingModal"));
        const passengerForm = document.getElementById("passengerDetailsForm");

        if (passengerForm) {
            let confirmedFlightInput = passengerForm.querySelector("input[name=\"confirmed_flight_data\"]");
            if (!confirmedFlightInput) {
                confirmedFlightInput = document.createElement("input");
                confirmedFlightInput.type = "hidden";
                confirmedFlightInput.name = "confirmed_flight_data";
                passengerForm.appendChild(confirmedFlightInput);
            }
            confirmedFlightInput.value = JSON.stringify(confirmedFlightData);
        }
        bookingModal.show();
    }

    // --- Handle Passenger Details Form Submission ---
    const passengerForm = document.getElementById("passengerDetailsForm");
    if (passengerForm) {
        passengerForm.addEventListener("submit", function(event) {
            event.preventDefault();
            const bookingModalInstance = bootstrap.Modal.getInstance(
                document.getElementById("flightBookingModal")
            );

            const formData = new FormData(passengerForm);
            const passengers = [{
                title: formData.get("passenger_title_1"),
                first_name: formData.get("passenger_firstname_1"),
                last_name: formData.get("passenger_lastname_1"),
                dob: formData.get("passenger_dob_1"),
                type: "ADT"
            }];
            const contactDetails = {
                email: formData.get("contact_email"),
                phone: formData.get("contact_phone")
            };

            const confirmedFlightDataString = formData.get("confirmed_flight_data");
            if (!confirmedFlightDataString) {
                displayError(document.getElementById("bookingModalMessages"),
                             "Confirmed flight data is missing. Cannot proceed.");
                return;
            }
            const confirmedFlightData = JSON.parse(confirmedFlightDataString);

            const bookingPayload = {
                booking: confirmedFlightData,
                passengers: passengers,
                contact: contactDetails
            };

            const submitButton = passengerForm.querySelector("button[type='submit']");
            submitButton.disabled = true;
            submitButton.textContent = "Booking...";

            axios.post(`{{ route("flight.book") }}`, bookingPayload)
                .then(function(response) {
                    submitButton.disabled = false;
                    submitButton.textContent = "Confirm Booking";
                    bookingModalInstance.hide();

                    if (response.data && response.data.booking_reference) {
                        displaySuccess(
                            bookingConfirmationDiv,
                            `Booking confirmed! Reference: ${response.data.booking_reference}`
                        );
                    } else if (response.data && response.data.message) {
                        displaySuccess(bookingConfirmationDiv, response.data.message);
                    } else {
                        displaySuccess(bookingConfirmationDiv, "Booking completed successfully.");
                    }
                })
                .catch(function(error) {
                    submitButton.disabled = false;
                    submitButton.textContent = "Confirm Booking";

                    let errorMessage = "An error occurred during booking.";
                    if (error.response && error.response.data) {
                        if (error.response.data.error) {
                            errorMessage = error.response.data.error;
                        }
                        if (error.response.data.message) {
                            errorMessage += ": " + error.response.data.message;
                        }
                    }
                    displayError(
                        document.getElementById("bookingModalMessages"),
                        errorMessage
                    );
                    console.error("Booking error:", error);
                });
        });
    }
});
</script>
@endpush
