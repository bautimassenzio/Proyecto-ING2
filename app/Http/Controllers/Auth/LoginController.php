<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    

    public function showLoginForm(){
        return view('/auth/login');
    }

    function definirLayout ($user) {
        $layout = match ($user->rol) {
            'cliente' => 'layouts.cliente',
            'empleado' => 'layouts.empleado',
            'administrador' => 'layouts.admin',
            default => 'layouts.base',
        };
    
        return view('inicio', compact('layout'));
        }

    public function login (Request $request){
        $credentials = $request->only('email', 'password');
        $user= \App\Domain\User\Models\Usuario::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user-> contraseña)){
            Auth::guard('users')->login($user);
            $request->session()->regenerate();
            return $this->definirLayout($user);
        }
        else {
            return redirect ('/fail');
        }
    }

    public function logout(Request $request){
        Auth::guard('users')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    
}
