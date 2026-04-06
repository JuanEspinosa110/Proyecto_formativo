<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$docs = \App\Models\Documento::whereNotNull('archivo')->latest('id_documento')->take(5)->get();
foreach($docs as $d) {
    $exists = Illuminate\Support\Facades\Storage::disk('uploads')->exists($d->archivo) ? 'Y' : 'N';
    echo $d->id_documento . ' -> ' . $d->archivo . ' exists: ' . $exists . PHP_EOL;
}
