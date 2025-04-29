<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlightProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FlightProviderController extends Controller
{
    /**
     * Display the flight provider settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $provider = FlightProvider::first();
        
        // If no provider exists, create a default one
        if (!$provider) {
            $provider = FlightProvider::create([
                'name' => 'Seeru Flights',
                'api_email' => config('services.seeru.email'),
                'api_password' => config('services.seeru.password'),
                'agency_code' => config('services.seeru.agency_code'),
                'api_base_url' => config('services.seeru.endpoint'),
                'enabled' => true,
                'service_class' => 'App\\Services\\Flights\\SeeruFlightsService'
            ]);
        }
        
        return view('admin.flight.provider.index', compact('provider'));
    }

    /**
     * Update the flight provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'api_email' => 'required|email|max:255',
            'api_password' => 'required|string|max:255',
            'agency_code' => 'required|string|max:255',
            'api_base_url' => 'required|string|max:255',
            'enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $provider = FlightProvider::first();
        
        // If no provider exists, create a new one
        if (!$provider) {
            $provider = new FlightProvider();
            $provider->service_class = 'App\\Services\\Flights\\SeeruFlightsService';
        }
        
        $provider->name = $request->name;
        $provider->api_email = $request->api_email;
        $provider->api_password = $request->api_password;
        $provider->agency_code = $request->agency_code;
        $provider->api_base_url = $request->api_base_url;
        $provider->enabled = $request->has('enabled');
        $provider->save();
        
        return back()->with('success', __('Flight provider updated successfully'));
    }
}
