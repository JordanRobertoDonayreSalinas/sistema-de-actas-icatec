<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class CronogramaActividadesExport implements WithEvents, WithTitle
{
    protected $actividades;

    // Altura por defecto de cada fila de datos (en puntos)
    const ROW_HEIGHT = 100;

    public function __construct($actividades)
    {
        $this->actividades = $actividades;
    }

    public function title(): string
    {
        return 'Cronograma de Actividades';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ============================================================
                // CABECERA (fila 1)
                // ============================================================
                $headers = [
                    'A1' => 'FECHA DE ACTIVIDAD',
                    'B1' => 'DESCRIPCIÓN DE ACTIVIDAD',
                    'C1' => 'ESTABLECIMIENTO EN DONDE SE REALIZA LA ACTIVIDAD',
                    'D1' => 'PARTICIPANTES EN LA ACTIVIDAD',
                    'E1' => 'ACTA O EVIDENCIA DE LA ACTIVIDAD REALIZADA',
                ];

                foreach ($headers as $cell => $value) {
                    $sheet->setCellValue($cell, $value);
                }

                // Fusionar E1:F1 para que el título abarque ambas columnas
                $sheet->mergeCells('E1:F1');

                // Estilo cabecera
                $headerStyle = [
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size'  => 10,
                        'name'  => 'Calibri',
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E3A5F'], // azul marino oscuro
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'AAAAAA'],
                        ],
                    ],
                ];
                $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
                $sheet->getRowDimension(1)->setRowHeight(36);

                // Anchos de columna
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(45);
                $sheet->getColumnDimension('C')->setWidth(38);
                $sheet->getColumnDimension('D')->setWidth(48);
                $sheet->getColumnDimension('E')->setWidth(40);
                $sheet->getColumnDimension('F')->setWidth(46);

                // ============================================================
                // FILAS DE DATOS
                // ============================================================
                $rowNum = 2;

                foreach ($this->actividades as $fila) {
                    $fecha = Carbon::parse($fila['fecha'])->format('d/m/Y');

                    // Col A — Fecha
                    $sheet->setCellValue("A{$rowNum}", $fecha);

                    // Col B — Descripción según tipo de acta
                    if ($fila['tipo_key'] === 'asistencia') {
                        // Capitalizar el texto de la actividad independientemente
                        $actividadTxt = $fila['actividad'] !== '—' ? $fila['actividad'] : '';
                        $actividadTxt = mb_strtolower($actividadTxt, 'UTF-8');
                        $actividadTxt = mb_strtoupper(mb_substr($actividadTxt, 0, 1, 'UTF-8'), 'UTF-8')
                                      . mb_substr($actividadTxt, 1, null, 'UTF-8');
                        $descripcion = 'Asistencia Técnica: ' . $actividadTxt;
                        if (!empty($fila['modalidad']) && $fila['modalidad'] !== '—') {
                            $descripcion .= "\nModalidad: " . $fila['modalidad'];
                        }
                    } elseif ($fila['tipo_key'] === 'monitoreo') {
                        $descripcion = 'Monitoreo de Uso del SIHCE MINSA - Presencial';
                    } else {
                        // implementacion
                        $descripcion = 'Implementación Módulo de ' . $fila['actividad'];
                    }
                    $sheet->setCellValue("B{$rowNum}", $descripcion);

                    // Col C — Establecimiento con prefijo P.S./C.S. según categoría
                    $categoria = strtoupper(trim($fila['categoria_establecimiento'] ?? ''));
                    if (in_array($categoria, ['I-1', 'I-2'])) {
                        $prefijo = 'P.S.';
                    } elseif (in_array($categoria, ['I-3', 'I-4'])) {
                        $prefijo = 'C.S.';
                    } else {
                        $prefijo = '';
                    }
                    $nombreEstab = $fila['establecimiento'];
                    // Convertir a título (Title Case) si está en mayúsculas
                    if (mb_strtoupper($nombreEstab, 'UTF-8') === $nombreEstab) {
                        $nombreEstab = mb_convert_case($nombreEstab, MB_CASE_TITLE, 'UTF-8');
                    }
                    $estabTxt = ($prefijo ? $prefijo . ' ' : '') . $nombreEstab . ' - ' . $fila['provincia'];
                    $sheet->setCellValue("C{$rowNum}", $estabTxt);

                    // Col D — Participantes
                    $participantesTxt = $fila['participantes_txt'] ?? $fila['responsable'];
                    $sheet->setCellValue("D{$rowNum}", $participantesTxt);

                    // Col E — Nombre del acta (texto) + imagen si existe
                    $actaTxt = $fila['nombre_acta'] ?? ('Acta de ' . $fila['tipo']);
                    $sheet->setCellValue("E{$rowNum}", $actaTxt);

                    // Altura de fila
                    $sheet->getRowDimension($rowNum)->setRowHeight(self::ROW_HEIGHT);

                    // Estilo de celda de dato
                    $cellStyle = [
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D9EAD3'], // verde claro (como en la imagen)
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                            'wrapText'   => true,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => 'AAAAAA'],
                            ],
                        ],
                        'font' => [
                            'size' => 9,
                            'name' => 'Calibri',
                        ],
                    ];
                    $sheet->getStyle("A{$rowNum}:F{$rowNum}")->applyFromArray($cellStyle);

                    // Columna B y D alineadas a la izquierda para facilitar la lectura
                    $sheet->getStyle("B{$rowNum}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("D{$rowNum}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    // Incrustar imágenes en columna F
                    $offsetX = 4;
                    if (!empty($fila['imagenes_paths']) && is_array($fila['imagenes_paths'])) {
                        foreach ($fila['imagenes_paths'] as $imgPath) {
                            try {
                                $drawing = new Drawing();
                                $drawing->setPath($imgPath);
                                $drawing->setCoordinates("F{$rowNum}");
                                $drawing->setOffsetX($offsetX);
                                $drawing->setOffsetY(8);
                                // Set image size
                                $drawing->setWidth(160);
                                $drawing->setHeight(80);
                                $drawing->setWorksheet($sheet);
                                
                                // Increment X offset for the next image horizontally
                                $offsetX += 166;
                            } catch (\Exception $e) {
                                // Si la imagen falla, continuamos con la siguiente
                            }
                        }
                    }

                    $rowNum++;
                }

                // ============================================================
                // Freeze panes y zoom
                // ============================================================
                $sheet->freezePane('A2');
                $sheet->getSheetView()->setZoomScale(80);

                // Ajuste de página para impresión
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}
