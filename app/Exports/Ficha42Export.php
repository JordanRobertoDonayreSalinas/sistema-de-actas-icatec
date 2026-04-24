<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Ficha42Export implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;
    protected $rowIndex = 0;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'ANEXO 1 (CM 42)';
    }

    public function headings(): array
    {
        return [
            ['ANEXO 1: LISTADO DE EESS CON EQUIPAMIENTO Y CONECTIVIDAD IMPLEMENTADA'],
            [],
            [
                'Orden',
                'DISA / DIRESA',
                'Categoría',
                'Código RENIPRESS',
                'Nombre del Establecimiento de Salud (EESS)',
                'Tipo de Equipo',
                'ACTUAL: TRIAJE',
                'ACTUAL: CONSULTORIO',
                'ACTUAL: VENTANILLA UNICA, CAJA Y ADMISION',
                'ACTUAL: PROGRAMACION',
                'ACTUAL: ACCESO RED',
                'ACTUAL: INTERNET',
                'FALTANTE: TRIAJE',
                'FALTANTE: CONSULTORIO',
                'FALTANTE: VENTANILLA UNICA, CAJA Y ADMISION',
                'FALTANTE: PROGRAMACION',
                'FALTANTE: ACCESO RED',
                'FALTANTE: INTERNET',
                'Gestión del Requerimiento'
            ]
        ];
    }

    public function map($row): array
    {
        // Solo incrementamos el orden si hay nombre de EESS (es la primera fila del grupo)
        if (!empty($row['nombre'])) {
            $this->rowIndex++;
            $orden = $this->rowIndex;
            $disa = 'ICA';
        } else {
            $orden = '';
            $disa = '';
        }
        
        return [
            $orden,
            $disa,
            $row['categoria'],
            $row['codigo'],
            $row['nombre'],
            $row['tipo_equipo'],
            $row['triaje'] ?: '',
            $row['consultorio'] ?: '',
            $row['admision'] ?: '',
            $row['programacion'] ?: '',
            (!empty($row['nombre']) && $row['red'] > 0) ? 'SI' : '',
            (!empty($row['nombre']) && $row['internet'] > 0) ? 'SI' : '',
            '', // Faltantes
            '',
            '',
            '',
            '',
            '',
            ''  // Gestión
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Combinar celdas del título
        $sheet->mergeCells('A1:S1');
        
        // Estilo del título principal
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Estilo de cabeceras de tabla (Fila 3)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 9,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'], // Azul oscuro
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A3:S3')->applyFromArray($headerStyle);
        $sheet->getRowDimension(3)->setRowHeight(45);

        // Bordes para los datos
        $dataRange = 'A4:S' . (count($this->data) + 3);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Alinear columnas de cantidades al centro
        $sheet->getStyle('G4:S' . (count($this->data) + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // Orden
            'B' => 15,  // DISA
            'C' => 10,  // Cat
            'D' => 12,  // Código
            'E' => 40,  // Nombre
            'F' => 25,  // Tipo equipo
            'G' => 12,  // Triaje
            'H' => 12,  // Consultorio
            'I' => 12,  // Admisión
            'J' => 12,  // Programacion
            'K' => 10,  // Red
            'L' => 10,  // Internet
            'M' => 12,  // F. Triaje
            'N' => 12,  // F. Cons
            'O' => 12,  // F. Adm
            'P' => 12,  // F. Prog
            'Q' => 10,  // F. Red
            'R' => 10,  // F. Int
            'S' => 30,  // Gestión
        ];
    }
}
