<?php

namespace App\Http\Controllers\Web\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use App\Mail\CodigoVerificacionMail;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{

    // Verificacion de codigo enviado al mail del admin para inicio de sesion
    public function CodigoVerificacionMail(Request $request) {
        $codigoIngresado = trim($request->codigo); 
        $user = Cache::get("codigo_verificacion_{$codigoIngresado}");
        if (!$user) {
            return back()->withErrors(['codigo' => 'El código es incorrecto o expiró']);
        }
        Auth::guard('users')->login($user);
        Cache::forget("codigo_verificacion_{$codigoIngresado}");
        $request->session()->regenerate();

        return LoginController::definirLayout($user);
    }      
        
    // Se ejecuta cuando el admin quiere iniciar sesion
    public static function isAdmin($user){
        session(['mail' => $user]);
        self::generarCodigo($user);
        return redirect('/confirmarAdmin');
    }

    // Genera un nuevo codigo para la autenticacion de 2 pasos del admin
    public function reenviarCodigo(){
        $user=session('mail'); 
        if (!$user) {
            return back()->withErrors(['user' => 'Error mail']); //Manejo de error, no deberia aparecer nunca
        }
        $codigoAnterior=session('codigo');
        Cache::forget("codigo_verificacion_{$codigoAnterior}"); //Elimino de cache el codigo anterior
        $this->generarCodigo($user);
        return redirect()->route('confirmarAdmin')->with('success', 'Código reenviado correctamente');
    }

    // Genera un codigo random con 5 minutos de validez para la autenticacion de 2 pasos del admin
    public static function generarCodigo($user){
        $codigo = rand(100000, 999999);
        session(['codigo' => $codigo]);
        Cache::put("codigo_verificacion_{$codigo}", $user, now()->addMinutes(5)); //Genero nuevo codigo y lo envio al mail
        Mail::to($user->email)->send(new CodigoVerificacionMail($user, $codigo));
    }

}