<?php

namespace App\Services;

use App\Models\Profesional;
use Illuminate\Support\Facades\Storage;

class SignatureService
{
    /**
     * Obtiene la ruta pública de la firma de un profesional por su DNI.
     * Retorna null si no tiene firma.
     */
    public function getSignatureByDni($dni)
    {
        if (empty($dni)) return null;

        $profesional = Profesional::where('doc', $dni)->first();

        if ($profesional) {
            $hasManualFile = $profesional->firma_path && Storage::disk('public')->exists($profesional->firma_path);
            
            // Si es DIGITAL y no tiene manual, retornamos el flag para generarla dinámicamente
            if ($profesional->tipo_firma === 'DIGITAL' || $hasManualFile) {
                return [
                    'path' => $profesional->firma_path,
                    'url' => $hasManualFile ? Storage::url($profesional->firma_path) : null,
                    'tipo' => $profesional->tipo_firma,
                    'is_manual' => $hasManualFile,
                    'profesional' => $profesional->apellido_paterno . ' ' . $profesional->apellido_materno . ' ' . $profesional->nombres,
                    'doc' => $profesional->doc,
                    'cargo' => $profesional->cargo
                ];
            }
        }

        return null;
    }

    /**
     * Obtiene las firmas de una lista de DNIs.
     * Útil para firmas automáticas masivas.
     */
    public function getMultipleSignatures(array $dnis)
    {
        $dnis = array_filter(array_unique($dnis));
        if (empty($dnis)) return collect();

        return Profesional::whereIn('doc', $dnis)
            ->get()
            ->filter(function($p) {
                return $p->tipo_firma === 'DIGITAL' || ($p->firma_path && Storage::disk('public')->exists($p->firma_path));
            })
            ->keyBy('doc');
    }
}
