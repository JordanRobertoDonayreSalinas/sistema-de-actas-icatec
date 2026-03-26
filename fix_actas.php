<?php
$tables = DB::select('SHOW TABLES LIKE "%_actas"');
$tables2 = DB::select('SHOW TABLES LIKE "%_implem_actas"');
$tables3 = DB::select('SHOW TABLES LIKE "%_usuarios_actas"');

$allTables = array_merge($tables, $tables2, $tables3);

foreach($allTables as $t) {
    $tableName = array_values((array)$t)[0];
    try {
        // Just in case it's not a primary key yet
        DB::statement("ALTER TABLE {$tableName} ADD PRIMARY KEY (id);");
    } catch(\Exception $e) {
        // ignore if already primary key
    }
    
    try {
        DB::statement("ALTER TABLE {$tableName} MODIFY id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;");
        echo "Fixed {$tableName}\n";
    } catch(\Exception $e) {
        echo "Error {$tableName}: " . $e->getMessage() . "\n";
    }
}
