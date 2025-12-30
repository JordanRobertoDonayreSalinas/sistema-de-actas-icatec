<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Acta;

class TriajeController extends Controller
{
    public function index($id){
        $acta = Acta::with('establecimiento')->findOrFail($id);
        return view('usuario.monitoreo.modulos.triaje', compact('acta'));
    }
}
