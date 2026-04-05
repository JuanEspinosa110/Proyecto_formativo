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
        if (DB::table('documentos')->exists()) {
            $this->command->info('DocumentoSeeder: Los datos ya existen, saltando...');
            return;
        }

        // 1. Obtener todos los conductores activos
        $conductores = DB::table('usuario')->where('id_tipo_usuario', 3)->where('id_estado', 1)->get();
        // 2. Obtener todos los buses
        $buses = DB::table('bus')->get();

        $documentos = [];

        // Licencias para conductores
        foreach ($conductores as $c) {
            $esVencido = rand(1, 10) === 1; // ~10% de probabilidad
            $fechaVencimiento = $esVencido 
                ? Carbon::now()->subMonths(rand(1, 6))->format('Y-m-d') 
                : Carbon::now()->addYears(rand(1, 5))->format('Y-m-d');
            
            $documentos[] = [
                'nombre' => 'LICENCIA DE CONDUCCIÓN - ' . $c->doc_usuario,
                'archivo' => 'uploads/documentos/seeders/licencia_default.png',
                'fecha_expedicion' => Carbon::parse($fechaVencimiento)->subYears(5)->format('Y-m-d'),
                'fecha_vencimiento' => $fechaVencimiento,
                'id_tipo_documento' => 3, // Licencia de Tránsito/Conducir
                'doc_usuario' => $c->doc_usuario,
                'NIT' => $c->NIT,
                'placa' => null,
                'id_estado' => $esVencido ? 8 : 1, // 8: VENCIDA, 1: ACTIVO
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // SOAT y Tecnomecánica para buses
        foreach ($buses as $b) {
            // Un 5% de probabilidad de no generar documentos para este bus
            if (rand(1, 20) === 1) {
                continue;
            }

            // SOAT
            $soatVencido = rand(1, 10) === 1; // ~10%
            $fechaVencimientoSoat = $soatVencido 
                ? Carbon::now()->subMonths(rand(1, 3))->format('Y-m-d') 
                : Carbon::now()->addMonths(rand(1, 11))->format('Y-m-d');

            $documentos[] = [
                'nombre' => 'SOAT - ' . $b->placa,
                'archivo' => 'uploads/documentos/seeders/soat_default.png',
                'fecha_expedicion' => Carbon::parse($fechaVencimientoSoat)->subYear()->format('Y-m-d'),
                'fecha_vencimiento' => $fechaVencimientoSoat,
                'id_tipo_documento' => 1, // SOAT
                'doc_usuario' => null,
                'NIT' => $b->NIT,
                'placa' => $b->placa,
                'id_estado' => $soatVencido ? 8 : 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Técnico Mecánica
            $tecnoVencido = rand(1, 10) === 1; // ~10%
            $fechaVencimientoTecno = $tecnoVencido 
                ? Carbon::now()->subMonths(rand(1, 3))->format('Y-m-d') 
                : Carbon::now()->addMonths(rand(1, 11))->format('Y-m-d');

            $documentos[] = [
                'nombre' => 'TECNOMECÁNICA - ' . $b->placa,
                'archivo' => 'uploads/documentos/seeders/tecno_default.png',
                'fecha_expedicion' => Carbon::parse($fechaVencimientoTecno)->subYear()->format('Y-m-d'),
                'fecha_vencimiento' => $fechaVencimientoTecno,
                'id_tipo_documento' => 2, // Tecno
                'doc_usuario' => null,
                'NIT' => $b->NIT,
                'placa' => $b->placa,
                'id_estado' => $tecnoVencido ? 8 : 1,
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
