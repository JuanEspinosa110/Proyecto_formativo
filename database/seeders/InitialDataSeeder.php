<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {

        // Estados completos
        $estados = [
            ['id_estado' => 1, 'nombre_estado' => 'ACTIVO'],
            ['id_estado' => 2, 'nombre_estado' => 'INACTIVO'],
            ['id_estado' => 3, 'nombre_estado' => 'SUSPENDIDO'],
            ['id_estado' => 4, 'nombre_estado' => 'EN MANTENIMIENTO'],
            ['id_estado' => 5, 'nombre_estado' => 'FINALIZADO'],
            ['id_estado' => 6, 'nombre_estado' => 'PENDIENTE'],
            ['id_estado' => 7, 'nombre_estado' => 'BLOQUEADA'],
            ['id_estado' => 8, 'nombre_estado' => 'VENCIDA'],
            ['id_estado' => 9, 'nombre_estado' => 'RENOVADA'],
        ];

        foreach ($estados as $estado) {
            DB::table('estado')->updateOrInsert(
                ['id_estado' => $estado['id_estado']],
                ['nombre_estado' => $estado['nombre_estado']]
            );
        }
    }
}



