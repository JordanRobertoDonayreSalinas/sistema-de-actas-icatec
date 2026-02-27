<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonitoreoModulos;
use Illuminate\Support\Facades\Log;

class ConectividadSeeder extends Seeder
{
    public function run()
    {
        $filePath = database_path('seeders/csv/insercion_conectividad.csv');
        
        if (!file_exists($filePath)) {
            $this->command->error("El archivo CSV no existe en: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        
        // 1. Omitir cabecera, indicando que el separador es el punto y coma (;)
        $header = fgetcsv($file, 0, ';'); 

        // 2. Leer fila por fila con el separador de punto y coma (;)
        while (($row = fgetcsv($file, 0, ';')) !== FALSE) {
            
            // VALIDACIÓN CLAVE: Si la fila no tiene al menos 7 columnas (0 al 6), 
            // significa que es una línea en blanco o está mal formada. La saltamos.
            if (count($row) < 7) {
                continue;
            }
            
            $cabeceraId = $row[0];
            $modulo     = $row[3];
            $tipo       = $row[4];
            $wifi       = $row[5] ?? '';
            $operador   = $row[6];

            // Buscar el registro existente
            $registro = MonitoreoModulos::where('cabecera_monitoreo_id', $cabeceraId)
                                        ->where('modulo_nombre', $modulo)
                                        ->first();

            if ($registro) {
                // Obtener el contenido actual
                $contenido = $registro->contenido ?? [];

                // Actualizar o insertar los campos específicos
                $contenido['tipo_conectividad'] = $tipo;
                $contenido['wifi_fuente']       = $wifi;
                $contenido['operador_servicio'] = $operador;

                // Guardar los cambios
                $registro->contenido = $contenido;
                $registro->save();

                $this->command->info("Actualizado: Acta $cabeceraId - Modulo: $modulo");
            } else {
                $this->command->warn("No se encontró registro para: Acta $cabeceraId - Modulo: $modulo");
            }
        }

        fclose($file);
    }
}