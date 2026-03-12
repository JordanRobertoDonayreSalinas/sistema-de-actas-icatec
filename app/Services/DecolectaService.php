<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DecolectaService
{
    /**
     * @var int Límite de consultas a Decolecta por mes
     */
    protected $monthlyLimit = 100;

    /**
     * @var string Prefijo para la llave en caché
     */
    protected $cachePrefix = 'decolecta_usage_';

    /**
     * @var string Token de acceso para Decolecta
     */
    protected $token = 'sk_13873.CXgdZqvgVRb7PK2BOOsMhRhQHmYRIDn7';

    /**
     * Realiza la consulta a RENIEC a través de Decolecta administrando el cupo mensual.
     *
     * @param string $doc Número de documento (DNI)
     * @return array|null Retorna un array con datos si es exitoso o null si falla/excede cupo
     */
    public function consultarDni($doc)
    {
        // 1. Validar límite
        if ($this->hasExceededMonthlyLimit()) {
            Log::warning("Límite mensual de validaciones en Decolecta excedido para este mes.");
            return [
                'error' => 'quota_exceeded',
                'message' => 'Límite mensual de validaciones excedido'
            ];
        }

        // 2. Ejecutar consulta
        try {
            $response = Http::withToken($this->token)
                ->withoutVerifying()
                ->timeout(10)
                ->get("https://api.decolecta.com/v1/reniec/dni?numero={$doc}");

            if ($response->successful() && $response->json('first_name')) {
                // 3. Incrementar contador local solo en solicitudes exitosas con datos reales
                $this->incrementUsage();

                $json = $response->json();
                
                // Lógica de separación de apellidos
                $paterno = '';
                $materno = '';
                
                if (isset($json['last_name']) && isset($json['second_name'])) {
                    $paterno = $json['last_name'];
                    $materno = $json['second_name'];
                } elseif (isset($json['full_name'])) {
                    $full = mb_strtoupper(trim($json['full_name']), 'UTF-8');
                    $nombres = mb_strtoupper(trim($json['first_name'] ?? ''), 'UTF-8');
                    
                    if ($nombres !== '') {
                        $apellidos_str = trim(str_replace($nombres, '', $full));
                        $apellidos_str = trim(str_replace(',', '', $apellidos_str));
                    } else {
                        $apellidos_str = $full;
                    }

                    $apellidos_arr = array_values(array_filter(explode(' ', $apellidos_str)));
                    if (count($apellidos_arr) > 1) {
                        $paterno = $apellidos_arr[0];
                        unset($apellidos_arr[0]);
                        $materno = implode(' ', $apellidos_arr);
                    } else {
                        $paterno = $apellidos_str;
                    }
                }

                return [
                    'success' => true,
                    'data' => [
                        'exists_external'  => true,
                        'tipo_doc'         => 'DNI',
                        'apellido_paterno' => $paterno,
                        'apellido_materno' => $materno,
                        'nombres'          => $json['first_name'] ?? '',
                        'email'            => '',
                        'telefono'         => '',
                        'remaining_tokens' => $this->getRemainingTokens(),
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error("Error consultando API externa DNI {$doc}: " . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Verifica si el contador del mes actual superó el límite
     */
    public function hasExceededMonthlyLimit(): bool
    {
        $currentMonthKey = $this->getCurrentMonthKey();
        $usage = Cache::get($currentMonthKey, 0);

        return $usage >= $this->monthlyLimit;
    }

    /**
     * Incrementa el uso en caché en 1 y establece su tiempo de vida
     */
    public function incrementUsage(): void
    {
        $currentMonthKey = $this->getCurrentMonthKey();
        
        // Si no existe, lo crea con 1 y le da una vida útil de 45 días para asegurarnos de cubrir el mes 
        if (!Cache::has($currentMonthKey)) {
            Cache::put($currentMonthKey, 1, now()->addDays(45));
        } else {
            Cache::increment($currentMonthKey);
        }
    }

    /**
     * Genera la llave para el mes en curso, ej. 'decolecta_usage_2026_03'
     */
    protected function getCurrentMonthKey(): string
    {
        return $this->cachePrefix . date('Y_m');
    }

    /**
     * Devuelve la cantidad de tokens (consultas) restantes en el mes
     */
    public function getRemainingTokens(): int
    {
        $currentMonthKey = $this->getCurrentMonthKey();
        $usage = Cache::get($currentMonthKey, 0);
        return max(0, $this->monthlyLimit - $usage);
    }
}
