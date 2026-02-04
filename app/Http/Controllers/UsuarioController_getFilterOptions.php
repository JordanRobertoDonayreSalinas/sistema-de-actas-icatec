/**
* AJAX: Obtener opciones de filtros según selección actual
*/
public function getFilterOptions(Request $request)
{
try {
$mes = $request->input('mes');
$anio = $request->input('anio');
$tipo = $request->input('tipo');
$provincia = $request->input('provincia');
$establecimientoId = $request->input('establecimiento_id');
$modulos = $request->input('modulos', []);

// Códigos y nombres de establecimientos especializados
$codigosCSMC = ['25933', '28653', '27197', '34021', '25977', '33478', '27199', '30478'];
$nombresCSMC = [
'CSMC TUPAC AMARU',
'CSMC COLOR ESPERANZA',
'CSMC DECÍDETE A SER FELIZ',
'CSMC SANTISIMA VIRGEN DE YAUCA',
'CSMC VITALIZA',
'CSMC CRISTO MORENO DE LUREN',
'CSMC NUEVO HORIZONTE',
'CSMC MENTE SANA'
];

// Construir query base
$query = \App\Models\EquipoComputo::query();

// Aplicar filtros de fecha
if ($mes && $anio) {
$query->whereHas('cabecera', function ($q) use ($mes, $anio) {
$q->whereMonth('fecha', $mes)->whereYear('fecha', $anio);
});
} elseif ($mes) {
$query->whereHas('cabecera', function ($q) use ($mes) {
$q->whereMonth('fecha', $mes);
});
} elseif ($anio) {
$query->whereHas('cabecera', function ($q) use ($anio) {
$q->whereYear('fecha', $anio);
});
}

// Aplicar filtro por Tipo de Establecimiento
if ($tipo) {
if ($tipo === 'ESPECIALIZADO') {
$query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
$q->where(function ($subQ) use ($codigosCSMC, $nombresCSMC) {
$subQ->whereIn('codigo', $codigosCSMC)
->orWhereIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
});
});
} elseif ($tipo === 'NO ESPECIALIZADO') {
$query->whereHas('cabecera.establecimiento', function ($q) use ($codigosCSMC, $nombresCSMC) {
$q->whereNotIn('codigo', $codigosCSMC)
->whereNotIn(DB::raw('UPPER(TRIM(nombre))'), $nombresCSMC);
});
}
}

// Aplicar filtro por Provincia
if ($provincia) {
$query->whereHas('cabecera.establecimiento', function ($q) use ($provincia) {
$q->where('provincia', $provincia);
});
}

// Aplicar filtro por Establecimiento
if ($establecimientoId) {
$query->whereHas('cabecera', function ($q) use ($establecimientoId) {
$q->where('establecimiento_id', $establecimientoId);
});
}

// Aplicar filtro por Módulos
if (!empty($modulos) && is_array($modulos)) {
$query->whereIn('modulo', $modulos);
}

// Obtener IDs de cabeceras y establecimientos con equipos filtrados
$cabecerasIds = (clone $query)->pluck('cabecera_monitoreo_id')->unique();
$establecimientosIds = \App\Models\CabeceraMonitoreo::whereIn('id', $cabecerasIds)
->pluck('establecimiento_id')
->unique();

// Obtener provincias disponibles
$provincias = \App\Models\Establecimiento::select('provincia')
->distinct()
->whereNotNull('provincia')
->whereIn('id', $establecimientosIds)
->orderBy('provincia')
->pluck('provincia')
->values();

// Obtener establecimientos disponibles
$establecimientos = \App\Models\Establecimiento::select('id', 'nombre', 'codigo')
->whereIn('id', $establecimientosIds)
->orderBy('nombre')
->get()
->map(function ($est) {
return [
'id' => $est->id,
'nombre' => $est->nombre,
'codigo' => $est->codigo
];
})
->values();

// Obtener módulos disponibles (de equipos filtrados)
$modulosDisponibles = (clone $query)
->select('modulo')
->distinct()
->whereNotNull('modulo')
->orderBy('modulo')
->pluck('modulo')
->map(function ($modulo) {
return [
'valor' => $modulo,
'nombre' => \App\Helpers\ModuloHelper::getNombreAmigable($modulo) ?? $modulo
];
})
->values();

// Obtener descripciones disponibles
$descripciones = (clone $query)
->select('descripcion')
->distinct()
->whereNotNull('descripcion')
->orderBy('descripcion')
->pluck('descripcion')
->values();

return response()->json([
'success' => true,
'provincias' => $provincias,
'establecimientos' => $establecimientos,
'modulos' => $modulosDisponibles,
'descripciones' => $descripciones
]);

} catch (\Exception $e) {
\Illuminate\Support\Facades\Log::error('Error in getFilterOptions: ' . $e->getMessage());

return response()->json([
'success' => false,
'error' => $e->getMessage()
], 500);
}
}