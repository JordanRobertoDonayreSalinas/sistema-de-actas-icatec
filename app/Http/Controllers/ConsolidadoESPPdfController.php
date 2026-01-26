<?php

namespace App\Http\Controllers;

use App\Models\CabeceraMonitoreo;
use App\Models\EquipoComputo;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsolidadoESPPdfController extends Controller
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
                : 'IMPLEMENTADOR / MONITOR'
        ];

        // 2. FUSIÓN DE DATOS
        $modulosNuevos = DB::table('mon_detalle_modulos')
            ->where('cabecera_monitoreo_id', $id)
            ->get()
            ->keyBy('modulo_nombre');

        $modulosAntiguos = DB::table('mon_monitoreo_modulos')
            ->where('cabecera_monitoreo_id', $id)
            ->get();

        $modulos = collect();
        
        // Filtramos 'config_modulos'
        $nombres = $modulosNuevos->keys()
            ->merge($modulosAntiguos->pluck('modulo_nombre'))
            ->unique()
            ->reject(function ($nombre) {
                return $nombre === 'config_modulos';
            });

        foreach($nombres as $nombre) {
            if ($modulosNuevos->has($nombre)) {
                $modulos->push($modulosNuevos->get($nombre));
            } else {
                $old = $modulosAntiguos->firstWhere('modulo_nombre', $nombre);
                if ($old) $modulos->push($old);
            }
        }

        // --- RENOMBRADO DE MÓDULOS ---
        $titulosMap = [
            'gestion_admin_esp'   => 'MODULO: GESTION ADMINISTRATIVA',
            'citas_esp'           => 'MODULO: CITAS',
            'triaje_esp'          => 'MODULO: TRIAJE',
            'toma_muestra'        => 'MODULO: TOMA DE MUESTRAS',
            'farmacia_esp'        => 'MODULO: FARMACIA',
            'sm_medicina_general' => 'MODULO: SALUD MENTAL / MEDICINA GENERAL',
            'sm_enfermeria'       => 'MODULO: SALUD MENTAL / ENFERMERIA'
        ];

        $modulos->transform(function($item) use ($titulosMap) {
            if (array_key_exists($item->modulo_nombre, $titulosMap)) {
                $item->modulo_nombre = $titulosMap[$item->modulo_nombre];
            } else {
                $item->modulo_nombre = mb_strtoupper(str_replace('_', ' ', $item->modulo_nombre), 'UTF-8');
            }
            return $item;
        });

        // 3. Equipos
        $equipos = EquipoComputo::where('cabecera_monitoreo_id', $id)->get();
        
        if ($equipos->isEmpty()) {
            foreach ($modulos as $mod) {
                $cont = is_string($mod->contenido) ? json_decode($mod->contenido, true) : $mod->contenido;
                $lista = $cont['equipos_de_computo'] ?? ($cont['equipos_data'] ?? ($cont['inventario'] ?? []));
                
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
            'modulos'         => $modulos,
            'equipos'         => $equipos,
            'equipoMonitoreo' => $equipoMonitoreo
        ];

        $pdf = Pdf::loadView('usuario.monitoreo.pdf_especializados.consolidado_pdf', $data);

        return $pdf->setPaper('a4', 'portrait')
                   ->stream("ACTA_ESP_CONSOLIDADA_N{$acta->id}.pdf");
    }
}