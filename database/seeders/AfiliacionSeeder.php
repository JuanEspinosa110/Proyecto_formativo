<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AfiliacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buses = DB::table('bus')->get();
        if ($buses->isEmpty()) return;

        foreach ($buses as $bus) {
            DB::table('afiliacion')->insert([
                'placa' => $bus->placa,
                'NIT' => $bus->NIT,
                'fecha_inicio' => now()->subYears(1),
                'fecha_fin' => now()->addYears(2),
                'id_estado' => 1 // ACTIVO
            ]);
        }
    }
}
