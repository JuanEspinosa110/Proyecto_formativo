<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    if (Schema::hasColumn('ruta', 'origen')) {
        DB::statement("ALTER TABLE `ruta` DROP COLUMN `origen` ");
        echo "Dropped origen\n";
    }
    if (Schema::hasColumn('ruta', 'destino')) {
        DB::statement("ALTER TABLE `ruta` DROP COLUMN `destino` ");
        echo "Dropped destino\n";
    }
    if (Schema::hasColumn('ruta', 'NIT')) {
         try {
            DB::statement("ALTER TABLE `ruta` DROP FOREIGN KEY `ruta_fk_empresa` ");
         } catch(Exception $e) {}
         DB::statement("ALTER TABLE `ruta` DROP COLUMN `NIT` ");
         echo "Dropped NIT\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
