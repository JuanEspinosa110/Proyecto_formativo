<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$table = 'ruta';
echo "--- STRUCTURE FOR TABLE: $table ---\n";
$columns = DB::select("DESCRIBE $table");
foreach ($columns as $column) {
    echo sprintf(
        "Field: %-20s Type: %-15s Null: %-5s Key: %-5s Default: %-10s Extra: %s\n",
        $column->Field,
        $column->Type,
        $column->Null,
        $column->Key,
        $column->Default,
        $column->Extra
    );
}

echo "\n--- FOREIGN KEYS ---\n";
$fks = DB::select("
    SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME = ? 
    AND TABLE_SCHEMA = DATABASE() 
    AND REFERENCED_TABLE_NAME IS NOT NULL
", [$table]);
foreach ($fks as $fk) {
    echo sprintf(
        "Column: %-20s References: %s(%s)\n",
        $fk->COLUMN_NAME,
        $fk->REFERENCED_TABLE_NAME,
        $fk->REFERENCED_COLUMN_NAME
    );
}
