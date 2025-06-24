<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

     // Verifica si hay algun usuario con sesion iniciada, sino redirecciona a la vista de inicio
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('/');
        }
    }
}
