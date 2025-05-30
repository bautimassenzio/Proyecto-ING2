<?php

namespace App\Http\Controllers\Web\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users;

class ViewsController extends Controller
{
    public function vistaWelcome(){
        return view('welcome');
    }

    public function vistaInicio() {
        $layout = session('layout', 'layouts.base'); // recupera o pone default
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

    
}