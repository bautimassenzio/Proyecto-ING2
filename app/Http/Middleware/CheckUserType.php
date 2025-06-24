<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
 
    // Chequeo de roles para acceso a funcionalidades
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::guard('users')->check()) {
            return redirect('/'); // Si no esta autenticado redirige a la vista de inicio
        }
        
        $userRole = Auth::guard('users')->user()->rol;
        
        // Se verifica si el rol del usuario actual esta dentro de la lista de roles permitidos poara la funcionalidad
        if (!in_array($userRole, $roles)) { 
            return redirect('/'); // Si no esta en la lista de roles permitidos redirige a la vista de inicio
        }

        return $next($request);
    }
}
