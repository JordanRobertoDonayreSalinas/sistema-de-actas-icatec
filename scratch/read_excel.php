<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$path = 'c:\Users\SoporteTI\Documents\GitHub\sistema-de-actas-icatec\Rina\[Modelo] Anexo 1 [CM 42]_ Listado de EESS con equipamiento y conectividad implementada.xlsx';

try {
    $spreadsheet = IOFactory::load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);
    
    // Print first 5 rows to see structure
    echo "--- STRUCTURE ---\n";
    for($i=1; $i<=5; $i++) {
        echo "Row $i: " . implode(" | ", $rows[$i]) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
