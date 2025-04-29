@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('Flight API Provider Settings') }}</h1>
        </div>
        @include('admin.message')
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-title"><strong>{{ __('Flight API Provider') }}</strong></div>
                    <div class="panel-body">
                        <form action="{{ route('flight.admin.provider.update') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Provider Name') }}</label>
                                        <input type="text" name="name" value="{{ $provider->name ?? 'Seeru Flights' }}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('API Email') }}</label>
                                        <input type="email" name="api_email" value="{{ $provider->api_email ?? config('services.seeru.email') }}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('API Password') }}</label>
                                        <input type="password" name="api_password" value="{{ $provider->api_password ?? config('services.seeru.password') }}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Agency Code') }}</label>
                                        <input type="text" name="agency_code" value="{{ $provider->agency_code ?? config('services.seeru.agency_code') }}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('API Base URL') }}</label>
                                        <input type="text" name="api_base_url" value="{{ $provider->api_base_url ?? config('services.seeru.endpoint') }}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="enabled" value="1" {{ ($provider && $provider->enabled) ? 'checked' : '' }}> {{ __('Enable Provider') }}
                                        </label>
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
