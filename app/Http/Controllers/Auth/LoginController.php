<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Estados;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\Roles;
use App\Http\Controllers\Web\Users\AdminController;
use App\Domain\User\Models\Usuario;



class LoginController extends Controller
{

    // Define el nav a usar segun el rol
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

    // Login de un usuario
    public function login (Request $request){
        $credentials = $request->only('email', 'password');
        $user= Usuario::where('email', $credentials['email'])
                        ->where('estado', Estados::ACTIVO) // Solo usuarios activos pueden ingresar
                        ->first();
        if(!$user) return back()->withErrors(['error' => 'Las credenciales ingresadas no son validas']);
        if (!Hash::check($credentials['password'], $user->contraseña))return back()->withErrors(['error' => 'Las credenciales ingresadas no son validas']);

            if ($user->rol == Roles::ADMINISTRADOR->value){
                return AdminController::isAdmin($user); // Si es admin va a la autenticacion de 2 pasos
            }
            else{ 
                Auth::guard('users')->login($user); // Si es cliente o empleado se inicia sesion
                $request->session()->regenerate();
                return $this->definirLayout($user);
            }    
    }

    // Cerrar sesion
    public function logout(Request $request){
        Auth::guard('users')->logout();
        session()->forget('layout');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    
}