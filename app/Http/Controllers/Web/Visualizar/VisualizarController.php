<?php

namespace App\Http\Controllers\Web\Visualizar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VisualizarController extends Controller
{
    public function mostrarPreguntasFrecuentes()
    {
        return view('Visualizar.preguntasfrecuentes'); 
    }

    public function mostrarInformacionContacto()
    {
        return view('Visualizar.infocontacto'); 
    }
}
