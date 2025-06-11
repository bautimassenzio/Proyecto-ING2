<?php

namespace App\Http\Controllers\Web\Visualizar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisualizarController extends Controller
{
    public function mostrarPreguntasFrecuentes()
    {
        $layout=session('layout','layouts.visitante');
        return view('Visualizar.preguntasfrecuentes',compact('layout')); 
    }

    public function mostrarInformacionContacto()
    {
        $layout=session('layout','layouts.visitante');
        return view('Visualizar.infocontacto',compact('layout')); 
    }
}
