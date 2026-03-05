<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\Empresa;
use App\Models\Bus;
use App\Models\Ruta;
use Illuminate\Support\Facades\DB;

try {
    echo "Starting Operational Flow Test...\n";

    $testNit = 1234567890;

    // 1. Create Empresa
    $empresa = Empresa::create([
        'NIT' => $testNit,
        'nombre_empresa' => 'Transportes Test SAS',
        'doc_representante' => 10101010,
        'primer_nombre_repre' => 'Juan',
        'primer_apellido_repre' => 'Perez',
        'segundo_apellido_repre' => 'Perez',
        'id_tipo_empresa' => 1,
        'id_estado' => 1,
        'id_ciudad' => '15001',
        'telefono_empresa' => '1234567',
        'correo_corporativo' => 'test@test.com',
        'telefono_representante' => '1234567',
        'correo_representante' => 'repre@test.com'
    ]);
    echo "- Empresa created: {$empresa->nombre_empresa}\n";

    // 2. Create Bus
    $bus = Bus::create([
        'placa' => 'ABC-123',
        'NIT' => $testNit,
        'modelo' => 'Toyota 2024',
        'capacidad_pasajeros' => 40,
        'kilometraje' => 100,
        'id_estado' => 1,
        'linc_transito' => 1234567,
        'numero_chasis' => 'CHASIS1234567890X',
        'numero_motro' => 'MOTOR123456789',
        'doc_propietario' => 99999,
        'nombre_propietario' => 'Owner',
        'telefono' => '12345',
        'correo' => 'owner@test.com'
    ]);
    echo "- Bus created: {$bus->placa}\n";

    // 3. Create Ruta
    $ruta = Ruta::create([
        'NIT'               => $testNit,
        'id_ciudad'         => '15001',
        'id_barrio_origen'  => 1,
        'id_barrio_destino' => 2,
        'origen'            => 'TERMINAL',
        'destino'           => 'CENTRO',
        'id_estado'         => 1
    ]);
    echo "- Ruta created: ID " . $ruta->id_ruta . "\n";

    // 4. Create Viaje
    $viajeId = DB::table('viaje')->insertGetId([
        'placa' => 'ABC-123',
        'id_ruta' => $ruta->id_ruta,
        'fecha' => now(),
        'id_estado' => 1
    ]);
    echo "- Viaje created: ID $viajeId\n";

    // 5. Verification
    echo "- Verifying relationships...\n";
    $busCountDb = Bus::where('placa', 'ABC-123')->count();
    $rutaCountDb = Ruta::where('id_ruta', $ruta->id_ruta)->count();
    echo "  - Bus exists: " . ($busCountDb ? 'YES' : 'NO') . "\n";
    echo "  - Ruta exists: " . ($rutaCountDb ? 'YES' : 'NO') . "\n";

    // 6. CASCADE DELETE
    echo "- Testing CASCADE DELETE (Deleting Empresa)...\n";
    $empresa->delete();

    $busCountFinal = Bus::where('placa', 'ABC-123')->count();
    $rutaCountFinal = Ruta::where('id_ruta', $ruta->id_ruta)->count();
    $viajeCountFinal = DB::table('viaje')->where('id_viaje', $viajeId)->count();

    echo "  - Bus exists after Empresa delete? " . ($busCountFinal ? 'YES' : 'NO (Cascaded)') . "\n";
    echo "  - Ruta exists after Empresa delete? " . ($rutaCountFinal ? 'YES' : 'NO (Cascaded)') . "\n";
    echo "  - Viaje exists after Empresa delete? " . ($viajeCountFinal ? 'YES' : 'NO (Cascaded)') . "\n";

    if ($busCountFinal == 0 && $rutaCountFinal == 0 && $viajeCountFinal == 0) {
        echo "\nSUCCESS: Full operational flow and CASCADE behavior validated.\n";
    } else {
        echo "\nFAILURE: Cascade delete did not work as expected.\n";
    }

} catch (\Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}
