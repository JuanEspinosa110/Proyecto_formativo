<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['id_estado' => 1, 'nombre_estado' => 'Activo'],
            ['id_estado' => 2, 'nombre_estado' => 'Inactivo'],
            ['id_estado' => 3, 'nombre_estado' => 'Suspendido'],
            ['id_estado' => 4, 'nombre_estado' => 'En mantenimiento'],
            ['id_estado' => 5, 'nombre_estado' => 'Finalizado'],
            ['id_estado' => 6, 'nombre_estado' => 'Pendiente'],
            ['id_estado' => 7, 'nombre_estado' => 'Bloqueada'],
            ['id_estado' => 8, 'nombre_estado' => 'Vencida'],
            ['id_estado' => 9, 'nombre_estado' => 'Renovada'],
        ];

        foreach ($estados as $estado) {
            DB::table('estado')->updateOrInsert(
                ['id_estado' => $estado['id_estado']],
                ['nombre_estado' => $estado['nombre_estado']]
            );
        }
    }
}
