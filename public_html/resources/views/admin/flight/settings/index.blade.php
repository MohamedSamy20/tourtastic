@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('Flight Settings') }}</h1>
        </div>
        @include('admin.message')
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-title"><strong>{{ __('Ticket Issuance Settings') }}</strong></div>
                    <div class="panel-body">
                        <form action="{{ route('flight.admin.settings.update') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="auto_issue_ticket" value="1" {{ $settings->auto_issue_ticket ? 'checked' : '' }}> 
                                            {{ __('Auto-issue tickets after payment') }}
                                        </label>
                                        <div class="form-text text-muted">
                                            {{ __('If enabled, tickets will be automatically issued after payment. If disabled, bookings will be held and marked as "under review".') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
