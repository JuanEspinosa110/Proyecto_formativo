<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener todos los conductores activos con rol 3
        $conductores = DB::table('usuario')->where('id_tipo_usuario', 3)->get();
        // 2. Obtener todos los buses
        $buses = DB::table('bus')->get();

        $documentos = [];

        // Licencias para conductores (Unificadas)
        foreach ($conductores as $c) {
            $documentos[] = [
                'nombre' => 'LICENCIA CONDUCCIÓN - ' . $c->doc_usuario,
                'archivo' => 'uploads/documentos/licencia_default.png',
                'fecha_expedicion' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                'fecha_vencimiento' => Carbon::now()->addYears(3)->format('Y-m-d'),
                'id_tipo_documento' => 3, 
                'doc_usuario' => $c->doc_usuario,
                'NIT' => $c->NIT,
                'placa' => null,
                'id_estado' => 1, 
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // SOAT y Tecnomecánica para buses
        foreach ($buses as $b) {
            // SOAT
            $documentos[] = [
                'nombre' => 'SOAT - ' . $b->placa,
                'archivo' => 'uploads/documentos/licencia_default.png',
                'fecha_expedicion' => Carbon::now()->subMonths(10)->format('Y-m-d'),
                'fecha_vencimiento' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                'id_tipo_documento' => 1, // SOAT
                'doc_usuario' => null,
                'NIT' => $b->NIT,
                'placa' => $b->placa,
                'id_estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Técnico Mecánica
            $documentos[] = [
                'nombre' => 'TECNOMECÁNICA - ' . $b->placa,
                'archivo' => 'uploads/documentos/licencia_default.png',
                'fecha_expedicion' => Carbon::now()->subMonths(11)->format('Y-m-d'),
                'fecha_vencimiento' => Carbon::now()->addMonth()->format('Y-m-d'),
                'id_tipo_documento' => 2, // Tecno
                'doc_usuario' => null,
                'NIT' => $b->NIT,
                'placa' => $b->placa,
                'id_estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Limpiar para actualizar (o usar insertOrIgnore si preferible)
        DB::table('documentos')->insertOrIgnore($documentos);
    }
}
