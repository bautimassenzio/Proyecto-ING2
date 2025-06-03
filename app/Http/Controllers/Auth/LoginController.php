<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\Roles;
use App\Http\Controllers\Web\Users\AdminController;



class LoginController extends Controller
{


    public function showLoginForm(){
        return view('/auth/login');
    }

    public static  function definirLayout ($user) {
        $layout = match ($user->rol) {
            'cliente' => 'layouts.cliente',
            'empleado' => 'layouts.empleado',
            'admin' => 'layouts.admin',
            default => 'layouts.base',
        };
        session(['layout' => $layout]); // Guardás el layout en sesión
        return redirect()->route('/'); // Redirigís a la ruta GET
        }

    public function login (Request $request){
        $credentials = $request->only('email', 'password');
        $user= \App\Domain\User\Models\Usuario::where('email', $credentials['email'])->first();

        if(!$user) return back()->withErrors(['email' => 'El mail ingresado no esta registrado en el sistema']);
        if (!Hash::check($credentials['password'], $user->contraseña))return back()->withErrors(['password' => 'La contraseña ingresada es incorrecta']);

            if ($user->rol == Roles::ADMINISTRADOR->value){
                return AdminController::isAdmin($user);
            }
            else{ 
                Auth::guard('users')->login($user);
                $request->session()->regenerate();
                return $this->definirLayout($user);
            }    
    }

    public function logout(Request $request){
        Auth::guard('users')->logout();
        session()->forget('layout');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    
}