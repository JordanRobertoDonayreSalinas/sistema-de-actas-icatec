<?php
$files = [
    'app/Http/Controllers/ImplementacionController.php',
    'app/Http/Controllers/ActaController.php',
    'app/Http/Controllers/CitaController.php',
    'app/Http/Controllers/AuditoriaEquiposController.php',
    'app/Http/Controllers/CitaESPController.php',
    'app/Http/Controllers/ConsolidadoESPPdfController.php',
    'app/Http/Controllers/ConsolidadoPdfController.php',
    'app/Http/Controllers/ConsultaMedicinaController.php',
    'app/Http/Controllers/ConsultaNutricionController.php',
    'app/Http/Controllers/CredController.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    // Only replace simple strtoupper($var) that don't yet have mb_strtoupper
    // using a negative lookbehind is complex, so let's just do a manual replace:
    
    // Convert strtoupper( to mb_strtoupper(..., 'UTF-8')
    // We will do this carefully with regex
    $content = preg_replace_callback('/strtoupper\(([^)]+)\)/', function($matches) {
        $inner = $matches[1];
        if (strpos($inner, 'mb_strtoupper') !== false || strpos($inner, 'UTF-8') !== false) {
            return $matches[0]; // Already processed or complex
        }
        return "mb_strtoupper(" . $inner . ", 'UTF-8')";
    }, $content);
    
    file_put_contents($file, $content);
    echo "Fixed $file\n";
}
