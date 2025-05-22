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

    public function login (Request $request){
        $credentials = $request->only('email', 'password');
        $user= \App\Domain\User\Models\Usuario::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user-> contraseña)){
            Auth::guard('usuarios')->login($user);
            return redirect('/inicio');
        }
        else {
            return redirect ('/fail');
        }
    }

}
