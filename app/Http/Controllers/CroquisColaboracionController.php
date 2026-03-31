<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CroquisColaboracionController extends Controller
{
    /**
     * Paleta de colores para identificar usuarios.
     * Se asigna de forma consistente basado en user_id % cantidad.
     */
    private static array $COLORES = [
        '#ef4444', // rojo
        '#f97316', // naranja
        '#eab308', // amarillo
        '#22c55e', // verde
        '#14b8a6', // teal
        '#3b82f6', // azul
        '#8b5cf6', // violeta
        '#ec4899', // pink
        '#06b6d4', // cyan
        '#84cc16', // lime
    ];

    /**
     * POST /usuario/croquis/{actaId}/sync
     * El cliente manda su cursor + estado del canvas.
     * Almacena en BD y devuelve el estado de los demás colaboradores.
     */
    public function sync(Request $request, int $actaId)
    {
        $user = Auth::user();
        $ahora = Carbon::now();
        $colorIndex = ($user->id - 1) % count(self::$COLORES);
        $color = self::$COLORES[$colorIndex];

        // Nombre completo del usuario
        $userName = trim(($user->apellido_paterno ?? '') . ' ' . ($user->name ?? $user->username));

        // Guardar/actualizar estado de este usuario
        DB::table('croquis_colaboracion')->updateOrInsert(
            ['acta_id' => $actaId, 'user_id' => $user->id],
            [
                'user_name'    => $userName,
                'color'        => $color,
                'cursor_x'     => (float) ($request->input('cursor_x', 0)),
                'cursor_y'     => (float) ($request->input('cursor_y', 0)),
                'elements'     => json_encode($request->input('elements', [])),
                'connections'  => json_encode($request->input('connections', [])),
                'deleted_ids'  => json_encode($request->input('deletedIds', [])),
                'last_seen_at' => $ahora,
                'updated_at'   => $ahora,
                'created_at'   => $ahora,
            ]
        );

        // Limpiar sesiones "fantasma": inactivas más de 45 segundos (desconexión real o pestaña cerrada)
        DB::table('croquis_colaboracion')
            ->where('acta_id', $actaId)
            ->where('last_seen_at', '<', Carbon::now()->subSeconds(45))
            ->delete();

        // Obtener colaboradores ACTIVOS (menos yo mismo)
        // Considera activo a cualquiera que haya enviado un ping en los últimos 45 segundos
        $colaboradores = DB::table('croquis_colaboracion')
            ->where('acta_id', $actaId)
            ->where('user_id', '!=', $user->id)
            ->where('last_seen_at', '>=', Carbon::now()->subSeconds(45))
            ->orderBy('last_seen_at', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'user_id'     => $row->user_id,
                    'user_name'   => $row->user_name,
                    'color'       => $row->color,
                    'cursor_x'    => (float) $row->cursor_x,
                    'cursor_y'    => (float) $row->cursor_y,
                    'elements'    => json_decode($row->elements, true) ?? [],
                    'connections' => json_decode($row->connections, true) ?? [],
                    'deletedIds'  => json_decode($row->deleted_ids ?? '[]', true) ?? [],
                    'last_seen'   => $row->last_seen_at,
                ];
            });

        return response()->json([
            'ok'           => true,
            'colaboradores' => $colaboradores,
        ]);
    }

    /**
     * POST /usuario/croquis/{actaId}/leave
     * El cliente avisa que sale (vía sendBeacon al cerrar la pestaña).
     */
    public function leave(Request $request, int $actaId)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['ok' => false], 401);

        DB::table('croquis_colaboracion')
            ->where('acta_id', $actaId)
            ->where('user_id', $user->id)
            ->delete();

        return response()->json(['ok' => true]);
    }
}
