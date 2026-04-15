<?php
$local_file = 'local.sql';
$prod_file = 'produccion.sql';

function get_tables($file) {
    $content = file_get_contents($file);
    preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=/s', $content, $matches);
    $tables = [];
    foreach ($matches[1] as $index => $name) {
        if (!isset($tables[$name])) { // Some tables might be repeated in local.sql
            $tables[$name] = $matches[2][$index];
        }
    }
    return $tables;
}

$local_tables = get_tables($local_file);
$prod_tables = get_tables($prod_file);

echo "--- LOCAL TABLES ---\n";
echo implode(", ", array_keys($local_tables)) . "\n\n";

echo "--- PRODUCTION TABLES ---\n";
echo implode(", ", array_keys($prod_tables)) . "\n\n";

// Compare mon_profesionales
if (isset($local_tables['mon_profesionales']) && isset($prod_tables['mon_profesionales'])) {
    echo "--- COMPARISON: mon_profesionales ---\n";
    echo "LOCAL:\n" . trim($local_tables['mon_profesionales']) . "\n\n";
    echo "PROD:\n" . trim($prod_tables['mon_profesionales']) . "\n\n";
}
