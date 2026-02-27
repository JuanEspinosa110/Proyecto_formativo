<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$table = $argv[1] ?? 'barrio';
echo "--- STRUCTURE FOR TABLE: $table ---\n";
try {
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
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
