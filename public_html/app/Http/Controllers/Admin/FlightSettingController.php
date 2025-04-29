<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlightSetting;
use Illuminate\Http\Request;

class FlightSettingController extends Controller
{
    /**
     * Display the flight settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = FlightSetting::getSettings();
        
        return view('admin.flight.settings.index', compact('settings'));
    }

    /**
     * Update the flight settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $settings = FlightSetting::getSettings();
        $settings->auto_issue_ticket = $request->has('auto_issue_ticket');
        $settings->save();
        
        return back()->with('success', __('Flight settings updated successfully'));
    }
}
