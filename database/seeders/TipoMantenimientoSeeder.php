<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoMantenimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Preventivo'],
            ['nombre' => 'Correctivo'],
            ['nombre' => 'Predictivo'],
        ];

        DB::table('tipo_mantenimiento')->insert($tipos);
    }
}
