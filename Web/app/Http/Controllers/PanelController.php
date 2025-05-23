<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PanelController extends Controller
{
    public function systemStatus()
    {
        if (!Session::has('user') || !Session::get('user')['is_logged_in']) {
            return redirect('/login');
        }
        return view('panel.system-status');
    }

    public function alarmControl()
    {
        if (!Session::has('user') || !Session::get('user')['is_logged_in']) {
            return redirect('/login');
        }
        return view('panel.alarm-control');
    }

    public function limitsControl()
    {
        if (!Session::has('user') || !Session::get('user')['is_logged_in']) {
            return redirect('/login');
        }
        return view('panel.limits-control');
    }

    public function sensorsSettings()
    {
        if (!Session::has('user') || !Session::get('user')['is_logged_in']) {
            return redirect('/login');
        }
        return view('panel.sensors-settings');
    }

    public function alarmsHistory()
    {
        if (!Session::has('user') || !Session::get('user')['is_logged_in']) {
            return redirect('/login');
        }
        return view('panel.alarms-history');
    }
    
} 