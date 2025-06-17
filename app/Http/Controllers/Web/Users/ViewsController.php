<?php

namespace App\Http\Controllers\Web\Users;

use App\Http\Controllers\Controller;


class ViewsController extends Controller
{
    public function vistaWelcome(){
        return view('welcome');
    }

    public function vistaInicio() {
        $layout = session('layout', 'layouts.base'); // recupera o pone default
        if ($layout == 'layouts.base') return view('visitante');
        else return view('inicio', compact('layout'));
    }

    public function vistaIniciofallido() {
        return view('inicioFail');
    }

    public function vistaRegistro() {
        $layout= session('layout', 'layouts.visitante');
        return view('registrarCliente', compact('layout'));
    }

    public function vistaRegistroPorEmpleado() {
        $layout = session('layout', 'layouts.visitante');
        return view('registrarClientePorEmpleado', compact('layout'));
    }

    public function vistaCambioContraseña() {
        $layout= session('layout', 'layouts.base');
        return view('cambioContraseña', compact('layout'));
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

    public function vistaEliminarCuenta() {
        return view('eliminarCuenta');
    }

}
