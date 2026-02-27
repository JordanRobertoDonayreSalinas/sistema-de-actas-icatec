<?php

namespace App\Helpers;

class ModuloHelper
{
    /**
     * Mapeo de nombres técnicos de módulos a nombres amigables
     */
    public static function getNombreAmigable($moduloTecnico)
    {
        $modulosMaster = [
            // Módulos estándar (NO ESPECIALIZADOS)
            'gestion_administrativa' => 'Gestión Administrativa',
            'citas' => 'Citas',
            'triaje' => 'Triaje',
            'consulta_medicina' => 'Consulta Externa: Medicina',
            'consulta_odontologia' => 'Consulta Externa: Odontología',
            'consulta_nutricion' => 'Consulta Externa: Nutrición',
            'consulta_psicologia' => 'Consulta Externa: Psicología',
            'cred' => 'CRED',
            'inmunizaciones' => 'Inmunizaciones',
            'atencion_prenatal' => 'Atención Prenatal',
            'planificacion_familiar' => 'Planificación Familiar',
            'parto' => 'Parto',
            'puerperio' => 'Puerperio',
            'fua_electronico' => 'FUA Electrónico',
            'farmacia' => 'Farmacia',
            'referencias' => 'Refcon',
            'laboratorio' => 'Laboratorio',
            'urgencias' => 'Urgencias y Emergencias',

            // Módulos especializados (CSMC)
            'gestion_admin_esp' => 'Gestión Administrativa',
            'citas_esp' => 'Citas',
            'triaje_esp' => 'Triaje',
            'salud_mental_group' => 'Salud Mental',
            'toma_muestra' => 'Toma de Muestra',
            'farmacia_esp' => 'Farmacia',

            // Sub-módulos de Salud Mental
            'sm_medicina_general' => 'Medicina General',
            'sm_psiquiatria' => 'Psiquiatría',
            'sm_med_familiar' => 'Medicina Familiar y Comunitaria',
            'sm_psicologia' => 'Psicología',
            'sm_enfermeria' => 'Enfermería',
            'sm_servicio_social' => 'Servicio Social',
            'sm_terapias' => 'Terapia Lenguaje / Ocupacional',
        ];

        return $modulosMaster[$moduloTecnico] ?? $moduloTecnico;
    }

    /**
     * Obtiene todos los módulos ordenados
     */
    public static function getTodosLosModulos()
    {
        return [
            // Módulos estándar
            'gestion_administrativa' => 'Gestión Administrativa',
            'citas' => 'Citas',
            'triaje' => 'Triaje',
            'consulta_medicina' => 'Consulta Externa: Medicina',
            'consulta_odontologia' => 'Consulta Externa: Odontología',
            'consulta_nutricion' => 'Consulta Externa: Nutrición',
            'consulta_psicologia' => 'Consulta Externa: Psicología',
            'cred' => 'CRED',
            'inmunizaciones' => 'Inmunizaciones',
            'atencion_prenatal' => 'Atención Prenatal',
            'planificacion_familiar' => 'Planificación Familiar',
            'parto' => 'Parto',
            'puerperio' => 'Puerperio',
            'fua_electronico' => 'FUA Electrónico',
            'farmacia' => 'Farmacia',
            'referencias' => 'Refcon',
            'laboratorio' => 'Laboratorio',
            'urgencias' => 'Urgencias y Emergencias',

            // Módulos especializados (CSMC)
            'gestion_admin_esp' => 'Gestión Administrativa',
            'citas_esp' => 'Citas',
            'triaje_esp' => 'Triaje',
            'salud_mental_group' => 'Salud Mental',
            'toma_muestra' => 'Toma de Muestra',
            'farmacia_esp' => 'Farmacia',

            // Sub-módulos de Salud Mental
            'sm_medicina_general' => 'Medicina General',
            'sm_psiquiatria' => 'Psiquiatría',
            'sm_med_familiar' => 'Medicina Familiar y Comunitaria',
            'sm_psicologia' => 'Psicología',
            'sm_enfermeria' => 'Enfermería',
            'sm_servicio_social' => 'Servicio Social',
            'sm_terapias' => 'Terapia Lenguaje / Ocupacional',
        ];
    }

    /**
     * Determina si un establecimiento es ESPECIALIZADO (CSMC) o NO ESPECIALIZADO
     * Usa la misma lógica que MonitoreoController
     */
    public static function getTipoEstablecimiento($establecimiento)
    {
        if (!$establecimiento) {
            return 'NO ESPECIFICADO';
        }

        // Códigos de CSMC (Centros de Salud Mental Comunitarios)
        $codigosCSMC = ['25933', '28653', '27197', '34021', '25977', '33478', '27199', '30478'];

        // Nombres de CSMC
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

        $esEspecializado = in_array($establecimiento->codigo, $codigosCSMC) ||
            in_array(strtoupper(trim($establecimiento->nombre)), $nombresCSMC);

        return $esEspecializado ? 'ESPECIALIZADO' : 'NO ESPECIALIZADO';
    }

    /**
     * Obtiene la información de conectividad de una cabecera de monitoreo
     * buscando en sus detalles.
     */
    public static function getConectividadActa($cabecera)
    {
        $info = [
            'tipo' => 'N/A',
            'fuente' => 'N/A',
            'operador' => 'N/A'
        ];

        if (!$cabecera || !$cabecera->detalles) {
            return $info;
        }

        foreach ($cabecera->detalles as $detalle) {
            $contenido = $detalle->contenido;
            if (isset($contenido['tipo_conectividad'])) {
                $info['tipo'] = $contenido['tipo_conectividad'];
                $info['fuente'] = $contenido['wifi_fuente'] ?? 'N/A';
                $info['operador'] = $contenido['operador_servicio'] ?? 'N/A';
                break; // Asumimos que la conectividad es la misma para toda el acta
            }
        }

        return $info;
    }
}
