<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    public function index()
    {
        $isLoggedIn = false;
        $user = null;
        
        if (Session::has('user')) {
            $isLoggedIn = true;
            $user = Session::get('user');
        }
        
        return view('index', [
            'isLoggedIn' => $isLoggedIn,
            'user' => $user
        ]);
    }
}
