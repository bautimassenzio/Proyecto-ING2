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
        if ($layout == 'layouts.base') return view('users.visitante');
        return view('users.inicio', compact('layout'));
    }

    public function vistaIniciofallido() {
        return view('users.inicioFail');
    }

    public function vistaRegistro() {
        return view('users.registrarCliente');
    }

    public function vistaRegistroPorEmpleado() {
        return view('users.registrarClientePorEmpleado');
    }

    public function vistaCambioContraseña() {
        return view('users.cambioContraseña');
    }

    public function vistaConfirmarAdmin() {
        return view('users.confirmarAdmin');
    }

    public function exitoRegister() {
        return view('users.exitoRegistro');
    }
    
    public function vistaVisitante() {
        return view('users.visitante');
    }

    public function vistaEliminarCuenta() {
        return view('users.eliminarCuenta');
    }

}