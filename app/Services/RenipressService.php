<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RenipressService
{
    protected $baseUrl = 'http://renipress.susalud.gob.pe:8080/wb-renipress';
    protected $app20Url = 'http://app20.susalud.gob.pe:8080/registro-renipress-webapp';
    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * Consulta el portal de RENIPRESS para obtener datos de servicios vía JSON API interno.
     */
    public function getDatosEstablecimiento($codigo)
    {
        $idipress = str_pad($codigo, 8, '0', STR_PAD_LEFT);
        
        try {
            Log::info("Iniciando sincronización RENIPRESS Robusta (JSON) - ID: {$idipress}");

            $jar = new \GuzzleHttp\Cookie\CookieJar();
            $client = new \GuzzleHttp\Client([
                'cookies' => $jar,
                'headers' => [
                    'User-Agent' => $this->userAgent,
                    'Accept' => 'application/json, text/javascript, */*; q=0.01',
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Referer' => "{$this->app20Url}/ipress.htm?action=consultaPorCodInico&idipress={$idipress}&est=1",
                ],
                'timeout' => 25,
                'verify' => false 
            ]);

            // Paso 1: Establecer sesión (Handshake)
            $handshakeUrl = "{$this->app20Url}/ipress.htm?action=consultaPorCodInico&idipress={$idipress}&est=1";
            $client->get($handshakeUrl);

            // Paso 2: Petición POST al endpoint de carga de datos (JSON)
            // Este endpoint devuelve TODA la información de la IPRESS en una sola estructura
            $apiUrl = "{$this->app20Url}/ipress.htm?action=cargarIpress";
            
            $response = $client->request('POST', $apiUrl, [
                'form_params' => [
                    'idipress' => $idipress
                ]
            ]);

            $json = (string) $response->getBody();
            $result = json_decode($json, true);

            if (isset($result['mensaje']) && $result['mensaje'] === 'ok' && isset($result['datos'])) {
                $raw = $result['datos'];
                
                $data = [
                    'upss' => $this->mapJsonData($raw['p_crCURSOR_UPSS'] ?? []),
                    'servicios' => $this->mapJsonData($raw['p_crCURSOR_UPS'] ?? []),
                    'especialidades' => $this->mapJsonData($raw['p_crCURSOR_ESPECIALIDES'] ?? []),
                    'cartera' => $this->mapJsonData($raw['P_CURSORCARTERA'] ?? [])
                ];

                Log::info("Sincronización JSON exitosa para {$codigo}");
                return $data;
            }

            Log::warning("El API interno de RENIPRESS no devolvió datos válidos para {$codigo}. Mensaje: " . ($result['mensaje'] ?? 'Sin respuesta'));

        } catch (\Exception $e) {
            Log::error("Fallo sincronización JSON RENIPRESS para {$codigo}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Mapea los cursores JSON de SUSALUD al formato estándar del sistema.
     */
    protected function mapJsonData($cursor)
    {
        if (!is_array($cursor)) return [];

        $results = [];
        foreach ($cursor as $item) {
            // SUSALUD usa nombres de columnas en mayúsculas
            $codigo = $item['CODIGO'] ?? $item['CO_UPS'] ?? $item['CO_ESPECIALIDAD'] ?? $item['CU_CARTERA'] ?? '';
            $nombre = $item['NOMBRE'] ?? $item['DE_UPS'] ?? $item['DE_ESPECIALIDAD'] ?? $item['DE_CAR_SER'] ?? '';
            $estado = $item['ESTADO'] ?? $item['ES_UPS'] ?? $item['ES_ESTADO'] ?? '';

            if ($codigo && $nombre) {
                $results[] = [
                    'codigo' => trim($codigo),
                    'nombre' => trim($nombre),
                    'estado' => trim($estado)
                ];
            }
        }
        return $results;
    }
}
