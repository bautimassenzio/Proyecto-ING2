<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ViewsController extends Controller
{
    public function vistaWelcome(){
        return view('welcome');
    }

    public function vistaInicio() {
        $layout = session('layout', 'layouts.base'); // recupera o pone default
        if ($layout == 'layouts.base') return view('visitante');
        return view('inicio', compact('layout'));
    }

    public function vistaIniciofallido() {
        return view('inicioFail');
    }

    public function vistaRegistro() {
        return view('registrarCliente');
    }

    public function vistaRegistroPorEmpleado() {
        return view('registrarClientePorEmpleado');
    }

    public function vistaCambioContraseña() {
        return view('cambioContraseña');
    }

    public function vistaConfirmarAdmin() {
        return view('confirmarAdmin');
    }

    public function exitoRegister() {
        return view('exitoRegistro');
    }
    
    public function vistaVisitante() {
        return view('visitante');
    }

}
