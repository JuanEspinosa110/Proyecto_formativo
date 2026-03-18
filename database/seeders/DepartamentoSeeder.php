<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            ['id_departamento' => '05', 'nombre_departamento' => 'ANTIOQUIA'],
            ['id_departamento' => '08', 'nombre_departamento' => 'ATLANTICO'],
            ['id_departamento' => '11', 'nombre_departamento' => 'BOGOTA'],
            ['id_departamento' => '12', 'nombre_departamento' => 'CURCUMA'],
            ['id_departamento' => '13', 'nombre_departamento' => 'BOLIVAR'],
            ['id_departamento' => '15', 'nombre_departamento' => 'BOYACA'],
            ['id_departamento' => '17', 'nombre_departamento' => 'CALDAS'],
            ['id_departamento' => '18', 'nombre_departamento' => 'CAQUETA'],
            ['id_departamento' => '19', 'nombre_departamento' => 'CAUCA'],
            ['id_departamento' => '20', 'nombre_departamento' => 'CESAR'],
            ['id_departamento' => '23', 'nombre_departamento' => 'CORDOBA'],
            ['id_departamento' => '25', 'nombre_departamento' => 'CUNDINAMARCA'],
            ['id_departamento' => '27', 'nombre_departamento' => 'CHOCO'],
            ['id_departamento' => '41', 'nombre_departamento' => 'HUILA'],
            ['id_departamento' => '44', 'nombre_departamento' => 'LA GUAJIRA'],
            ['id_departamento' => '47', 'nombre_departamento' => 'MAGDALENA'],
            ['id_departamento' => '50', 'nombre_departamento' => 'META'],
            ['id_departamento' => '52', 'nombre_departamento' => 'NARIÑO'],
            ['id_departamento' => '54', 'nombre_departamento' => 'NORTE DE SANTANDER'],
            ['id_departamento' => '63', 'nombre_departamento' => 'QUINDIO'],
            ['id_departamento' => '66', 'nombre_departamento' => 'RISARALDA'],
            ['id_departamento' => '68', 'nombre_departamento' => 'SANTANDER'],
            ['id_departamento' => '70', 'nombre_departamento' => 'SUCRE'],
            ['id_departamento' => '73', 'nombre_departamento' => 'TOLIMA'],
            ['id_departamento' => '76', 'nombre_departamento' => 'VALLE DEL CAUCA'],
            ['id_departamento' => '81', 'nombre_departamento' => 'ARAUCA'],
            ['id_departamento' => '85', 'nombre_departamento' => 'CASANARE'],
            ['id_departamento' => '86', 'nombre_departamento' => 'PUTUMAYO'],
            ['id_departamento' => '88', 'nombre_departamento' => 'SAN ANDRES'],
            ['id_departamento' => '91', 'nombre_departamento' => 'AMAZONAS'],
            ['id_departamento' => '94', 'nombre_departamento' => 'GUAINIA'],
            ['id_departamento' => '95', 'nombre_departamento' => 'GUAVIARE'],
            ['id_departamento' => '97', 'nombre_departamento' => 'VAUPES'],
            ['id_departamento' => '99', 'nombre_departamento' => 'VICHADA']
        ];
        
        // Optimización: Insertar o ignorar si ya existe
        \Illuminate\Support\Facades\DB::table('departamento')->insertOrIgnore($departamentos);
    }
}
