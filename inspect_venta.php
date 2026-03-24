<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $columns = DB::select("SHOW COLUMNS FROM venta_viaje");
    foreach ($columns as $c) {
        echo $c->Field . ' - ' . $c->Type . " - " . ($c->Key ?? '') . " - " . ($c->Extra ?? '') . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
