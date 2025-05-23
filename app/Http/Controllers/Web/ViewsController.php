<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ViewsController extends Controller
{
    public function vistaWelcome(){
        return view('welcome');
    }

    public function vistaInicio() {
        return view('inicio');
    }

    public function vistaIniciofallido() {
        return view('inicioFail');
    }


}
