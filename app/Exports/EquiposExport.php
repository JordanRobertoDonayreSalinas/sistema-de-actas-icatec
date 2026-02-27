<?php

namespace App\Exports;

use App\Models\EquipoComputo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquiposExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $equipos;

    public function __construct($equipos)
    {
        $this->equipos = $equipos;
    }

    /**
     * Retorna la colección de equipos a exportar
     */
    public function collection()
    {
        return $this->equipos;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'IPRESS',
            'Establecimiento',
            'Categoría',
            'Tipo',
            'Módulo',
            'Cantidad',
            'Descripción',
            'NroSerie',
            'Propio',
            'Estado',
            'Provincia',
            'Distrito',
            'Conectividad',
            'Fuente WiFi',
            'Proveedor',
            'Observación',
        ];
    }

    public function map($equipo): array
    {
        return [
            $equipo->cabecera->fecha ? \Carbon\Carbon::parse($equipo->cabecera->fecha)->format('d/m/Y') : 'N/A',
            $equipo->cabecera->establecimiento->codigo ?? 'N/A',
            $equipo->cabecera->establecimiento->nombre ?? 'N/A',
            $equipo->cabecera->establecimiento->categoria ?? 'N/A',
            \App\Helpers\ModuloHelper::getTipoEstablecimiento($equipo->cabecera->establecimiento),
            \App\Helpers\ModuloHelper::getNombreAmigable($equipo->modulo) ?? 'N/A',
            $equipo->cantidad ?? 0,
            $equipo->descripcion ?? 'N/A',
            $equipo->nro_serie ?? 'S/N',
            // Solución aplicada al campo "propio" para evitar errores lógicos/booleanos en Excel
            $equipo->propio ?? 'N/A',
            $equipo->estado ?? 'N/A',
            $equipo->cabecera->establecimiento->provincia ?? 'N/A',
            $equipo->cabecera->establecimiento->distrito ?? 'N/A',
            \App\Helpers\ModuloHelper::getConectividadActa($equipo->cabecera)['tipo'],
            \App\Helpers\ModuloHelper::getConectividadActa($equipo->cabecera)['fuente'],
            \App\Helpers\ModuloHelper::getConectividadActa($equipo->cabecera)['operador'],
            $equipo->observacion ?? '',
        ];
    }

    /**
     * Aplica estilos a la hoja de Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila de encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6366F1'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Fecha
            'B' => 12,  // IPRESS
            'C' => 35,  // Establecimiento
            'D' => 12,  // Categoría
            'E' => 18,  // Tipo
            'F' => 30,  // Módulo
            'G' => 10,  // Cantidad
            'H' => 30,  // Descripción
            'I' => 15,  // Nro Serie (ligeramente ampliado para mayor comodidad)
            'J' => 12,  // Propio
            'K' => 12,  // Estado
            'L' => 15,  // Provincia
            'M' => 15,  // Distrito
            'N' => 18,  // Conectividad
            'O' => 18,  // Fuente WiFi
            'P' => 18,  // Proveedor
            'Q' => 45,  // Observación
        ];
    }
}
