<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
 
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::guard('users')->check()) {
            return redirect('/login');
        }
        
        $userRole = Auth::guard('users')->user()->rol;
        
        if (!in_array($userRole, $roles)) {
            return redirect('/login');
        }
    
        return $next($request);
}
}
