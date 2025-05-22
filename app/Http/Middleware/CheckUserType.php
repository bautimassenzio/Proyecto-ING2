<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
 
    public function handle(Request $request, Closure $next, $type)
    {
        if (Auth::guard('usuarios')->check() && Auth::guard('usuarios')->user()->rol != $type){
            return redirect('/api/users');
        }
    }
}
