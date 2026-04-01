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
        // 1. Obtener todos los conductores activos
        $conductores = DB::table('usuario')->where('id_tipo_usuario', 3)->where('id_estado', 1)->get();
        // 2. Obtener todos los buses
        $buses = DB::table('bus')->get();

        $documentos = [];

        // Licencias para conductores
        foreach ($conductores as $c) {
            $documentos[] = [
                'nombre' => 'LICENCIA DE CONDUCCIÓN - ' . $c->doc_usuario,
                'archivo' => 'seeders/licencia_default.jpg',
                'fecha_expedicion' => Carbon::now()->subYears(2)->format('Y-m-d'),
                'fecha_vencimiento' => Carbon::now()->addYears(3)->format('Y-m-d'),
                'id_tipo_documento' => 3, // Licencia de Tránsito/Conducir
                'doc_usuario' => $c->doc_usuario,
                'NIT' => $c->NIT,
                'placa' => null,
                'id_estado' => 1, // VIGENTE/ACTIVO
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // SOAT y Tecnomecánica para buses
        foreach ($buses as $b) {
            // SOAT
            $documentos[] = [
                'nombre' => 'SOAT - ' . $b->placa,
                'archivo' => 'seeders/soat_default.jpg',
                'fecha_expedicion' => Carbon::now()->subMonths(6)->format('Y-m-d'),
                'fecha_vencimiento' => Carbon::now()->addMonths(6)->format('Y-m-d'),
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
                'archivo' => 'seeders/tecno_default.jpg',
                'fecha_expedicion' => Carbon::now()->subMonths(8)->format('Y-m-d'),
                'fecha_vencimiento' => Carbon::now()->addMonths(4)->format('Y-m-d'),
                'id_tipo_documento' => 2, // Tecno
                'doc_usuario' => null,
                'NIT' => $b->NIT,
                'placa' => $b->placa,
                'id_estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar en bloques para evitar problemas de memoria si hay muchos
        foreach (array_chunk($documentos, 100) as $chunk) {
            DB::table('documentos')->insert($chunk);
        }
    }
}
