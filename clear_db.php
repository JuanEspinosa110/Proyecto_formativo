<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    $tables = DB::select('SHOW TABLES');
    $dbName = DB::connection()->getDatabaseName();
    
    foreach ($tables as $table) {
        $key = 'Tables_in_' . $dbName;
        $name = $table->$key ?? array_values((array)$table)[0];
        echo "Dropping $name...\n";
        DB::statement("DROP TABLE IF EXISTS `$name` CASCADE");
    }
    
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    echo "Base de datos vaciada con éxito.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
