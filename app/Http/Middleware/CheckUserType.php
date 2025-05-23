<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
 
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::guard('users')->check()) {
            return redirect('/login');
        }
        
        if (Auth::guard('users')->user()->rol != $role) {
            return redirect('/login');
        }
        
        return $next($request); // importantísimo: dejar pasar la petición cuando el rol sí coincide
    }
}
