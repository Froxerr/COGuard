<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckUserSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('user') || !Session::get('user')['is_logged_in']) {
            return redirect('/login');
        }

        return $next($request);
    }
} 