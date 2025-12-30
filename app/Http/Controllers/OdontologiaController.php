<?php

namespace App\Http\Controllers;
use App\Models\Acta;

use Illuminate\Http\Request;

class OdontologiaController extends Controller
{
    public function index($id){
        $acta = Acta::with('establecimiento')->findOrFail($id);
        return view('usuario.monitoreo.modulos.odontologia', compact('acta'));
    }
}
