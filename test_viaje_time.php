<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Viaje;

$viaje = Viaje::first();
if ($viaje) {
    echo "ID: " . $viaje->id_viaje . "\n";
    echo "Fecha original: " . $viaje->fecha . "\n";
    echo "Fecha parseada: " . \Carbon\Carbon::parse($viaje->fecha)->toDateTimeString() . "\n";
} else {
    echo "No hay viajes.\n";
}
