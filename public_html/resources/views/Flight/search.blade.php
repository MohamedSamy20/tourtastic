@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Flight Search Results') }}</div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(isset($flights) && count($flights) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Flight Number</th>
                                        <th>Departure</th>
                                        <th>Arrival</th>
                                        <th>Duration</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($flights as $flight)
                                        <tr>
                                            <td>{{ $flight['flight_number'] ?? 'N/A' }}</td>
                                            <td>
                                                {{ $flight['departure']['airport'] ?? 'N/A' }}<br>
                                                {{ $flight['departure']['time'] ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $flight['arrival']['airport'] ?? 'N/A' }}<br>
                                                {{ $flight['arrival']['time'] ?? 'N/A' }}
                                            </td>
                                            <td>{{ $flight['duration'] ?? 'N/A' }}</td>
                                            <td>{{ $flight['price']['total'] ?? 'N/A' }} {{ $flight['price']['currency'] ?? '' }}</td>
                                            <td>
                                                <a href="{{ route('flight.book', ['flight_id' => $flight['id']]) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    Book Now
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No flights found matching your criteria.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection