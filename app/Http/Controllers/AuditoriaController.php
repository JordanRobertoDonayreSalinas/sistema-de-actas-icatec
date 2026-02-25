<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentoAdministrativo;
use App\Models\Establecimiento;
use App\Helpers\ModuloHelper;
use Carbon\Carbon;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fecha_fin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        // 1. Obtener profesionales que marcaron SIHCE en tablas dedicadas
        $sihce_dedicadas = DB::table('mon_modulo_citas')
            ->select('personal_dni as doc', 'personal_nombre as nombre', DB::raw("'citas' as modulo"), 'monitoreo_id')
            ->where('utiliza_sihce', 'SI')
            ->union(
                DB::table('mon_modulo_prenatal')
                    ->select('personal_dni as doc', 'personal_nombre as nombre', DB::raw("'atencion_prenatal' as modulo"), 'monitoreo_id')
                    ->where('utiliza_sihce', 'SI')
            )
            ->union(
                DB::table('mon_modulo_parto')
                    ->select('personal_dni as doc', 'personal_nombre as nombre', DB::raw("'parto' as modulo"), 'monitoreo_id')
                    ->where('utiliza_sihce', 'SI')
            );

        // 2. Obtener profesionales de módulos estándar (com_docu_asisten)
        // Usamos 'gestion_administrativa' como nombre técnico base para estos profesionales
        $sihce_estandar = DB::table('com_docu_asisten')
            ->join('mon_profesionales', 'com_docu_asisten.profesional_id', '=', 'mon_profesionales.id')
            ->select('mon_profesionales.doc', DB::raw("CONCAT(mon_profesionales.nombres, ' ', mon_profesionales.apellido_paterno) as nombre"), DB::raw("'gestion_administrativa' as modulo"), 'com_docu_asisten.acta_id as monitoreo_id')
            ->where('com_docu_asisten.utiliza_sihce', 'SI');

        // 3. Obtener profesionales de módulos especializados (JSON en mon_monitoreo_modulos)
        // Nota: Querying JSON in DB can be slow, but for internal reports it's manageable.
        $especializados = DB::table('mon_monitoreo_modulos')
            ->where('modulo_nombre', 'LIKE', '%_esp')
            ->orWhere('modulo_nombre', 'LIKE', 'sm_%')
            ->get();

        $sihce_json = [];
        foreach ($especializados as $registro) {
            $cont = is_string($registro->contenido) ? json_decode($registro->contenido, true) : $registro->contenido;

            // Todos los módulos especializados (incluyendo sm_*) comparten la misma estructura base para el profesional
            $utiliza = data_get($cont, 'documentacion_administrativa.utiliza_sihce');
            if ($utiliza === 'SI' || $utiliza === 'SÍ') {
                $sihce_json[] = [
                    'doc' => data_get($cont, 'datos_del_profesional.doc'),
                    'nombre' => data_get($cont, 'datos_del_profesional.nombres') . ' ' . data_get($cont, 'datos_del_profesional.apellido_paterno'),
                    'modulo' => $registro->modulo_nombre,
                    'monitoreo_id' => $registro->cabecera_monitoreo_id
                ];
            }
        }

        // Combinar todos los orígenes
        $universo = $sihce_dedicadas->get()->toArray();
        $universo = array_merge($universo, $sihce_estandar->get()->toArray());
        $universo = array_merge($universo, $sihce_json);

        // Limpiar duplicados por DNI (Un profesional puede aparecer en varios módulos)
        $profesionalesSihce = [];
        foreach ($universo as $p) {
            $p = (object) $p;
            if (empty($p->doc))
                continue;
            if (!isset($profesionalesSihce[$p->doc])) {
                $profesionalesSihce[$p->doc] = $p;
            }
        }

        // 4. Cruzar con Documentos Administrativos
        $inconsistencias = [];
        foreach ($profesionalesSihce as $doc => $p) {
            $ad = DocumentoAdministrativo::where('profesional_doc', $doc)->first();

            // Solo mostrar si el registro NO EXISTE (faltan crear)
            if (!$ad) {
                // Obtener datos del monitoreo para contexto
                $cabecera = DB::table('mon_cabecera_monitoreo')
                    ->join('establecimientos', 'mon_cabecera_monitoreo.establecimiento_id', '=', 'establecimientos.id')
                    ->where('mon_cabecera_monitoreo.id', $p->monitoreo_id)
                    ->select('establecimientos.nombre as ipress', 'mon_cabecera_monitoreo.fecha', 'establecimientos.provincia', 'establecimientos.distrito')
                    ->first();

                $inconsistencias[] = [
                    'doc' => $doc,
                    'nombre' => $p->nombre,
                    'modulo_origen' => ModuloHelper::getNombreAmigable($p->modulo),
                    'ipress' => $cabecera->ipress ?? 'N/A',
                    'provincia' => $cabecera->provincia ?? 'N/A',
                    'distrito' => $cabecera->distrito ?? 'N/A',
                    'fecha_monitoreo' => $cabecera->fecha ?? 'N/A',
                    'estado_compromiso' => 'FALTA CREAR',
                    'estado_dj' => 'FALTA CREAR',
                ];
            }
        }

        // Filtros adicionales (Opcional, se pueden aplicar en la colección final)
        if ($request->filled('provincia')) {
            $inconsistencias = array_filter($inconsistencias, fn($i) => $i['provincia'] == $request->provincia);
        }

        return view('usuario.auditoria.index', [
            'inconsistencias' => $inconsistencias,
            'provincias' => Establecimiento::distinct()->pluck('provincia'),
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }
}
