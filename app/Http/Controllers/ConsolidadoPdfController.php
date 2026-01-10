<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsolidadoPdfController extends Controller
{
    public function generar($id)
    {
        // 1. Datos Generales
        $acta = CabeceraMonitoreo::with(['establecimiento', 'user'])->findOrFail($id);

        $jefe = [
            'nombre' => mb_strtoupper($acta->responsable ?? 'NO REGISTRADO', 'UTF-8'),
            'cargo'  => 'JEFE DE ESTABLECIMIENTO'
        ];
        
        $creador = $acta->user;
        $monitor = [
            'nombre' => $creador 
                ? mb_strtoupper("{$creador->apellido_paterno} {$creador->apellido_materno} {$creador->name}", 'UTF-8')
                : 'USUARIO NO IDENTIFICADO'
        ];

        // 2. FUSIÓN DE DATOS (CRÍTICO PARA QUE JALE TODO)
        // A. Tabla Nueva (Prioridad)
        $modulosNuevos = DB::table('mon_detalle_modulos')
            ->where('cabecera_monitoreo_id', $id)
            ->get()
            ->keyBy('modulo_nombre');

        // B. Tabla Antigua (Respaldo)
        $modulosAntiguos = DB::table('mon_monitoreo_modulos')
            ->where('cabecera_monitoreo_id', $id)
            ->get();

        // C. Combinación en una sola colección '$modulos'
        $modulos = collect();
        
        $nombres = $modulosNuevos->keys()
            ->merge($modulosAntiguos->pluck('modulo_nombre'))
            ->unique();

        foreach($nombres as $nombre) {
            if ($modulosNuevos->has($nombre)) {
                $modulos->push($modulosNuevos->get($nombre));
            } else {
                $old = $modulosAntiguos->firstWhere('modulo_nombre', $nombre);
                if ($old) $modulos->push($old);
            }
        }

        // 3. Equipos (Híbrido SQL + JSON para asegurar compatibilidad)
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->get();
        
        // Si no hay equipos en SQL, buscar dentro de los JSON de los módulos (Respaldo)
        if ($equipos->isEmpty()) {
            foreach ($modulos as $mod) {
                $cont = is_string($mod->contenido) ? json_decode($mod->contenido, true) : $mod->contenido;
                $lista = $cont['equipos_data'] ?? ($cont['inventario'] ?? []);
                if (is_array($lista) && count($lista) > 0) {
                    foreach ($lista as $item) {
                        if (!empty($item['descripcion'])) {
                            $obj = new \stdClass();
                            $obj->modulo = $mod->modulo_nombre;
                            $obj->descripcion = $item['descripcion'];
                            $obj->cantidad = $item['cantidad'] ?? 1;
                            $obj->estado = $item['estado'] ?? 'REGULAR';
                            $obj->propio = $item['propiedad'] ?? ($item['propio'] ?? 'ESTABLECIMIENTO');
                            $obj->nro_serie = $item['nro_serie'] ?? ($item['codigo'] ?? '-');
                            $equipos->push($obj);
                        }
                    }
                }
            }
        }

        // 4. Equipo de Acompañamiento
        $equipoMonitoreo = DB::table('mon_equipo_monitoreo')
            ->where('cabecera_monitoreo_id', $id)
            ->get();

        // 5. Preparar PDF
        $data = [
            'acta'            => $acta,
            'jefe'            => $jefe,
            'monitor'         => $monitor,
            'modulos'         => $modulos, // VARIABLE CORREGIDA (antes era modulosFinal)
            'equipos'         => $equipos,
            'equipoMonitoreo' => $equipoMonitoreo
        ];

        $pdf = Pdf::loadView('usuario.monitoreo.pdf.consolidado_pdf', $data);

        return $pdf->setPaper('a4', 'portrait')
                   ->stream("ACTA_CONSOLIDADA_N{$acta->id}.pdf");
    }
}