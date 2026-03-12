<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('tipo_usuario')->upsert([
            ['id_tipo_usuario' => 1, 'nombre_tipo' => 'Admin'],
            ['id_tipo_usuario' => 2, 'nombre_tipo' => 'Pasajero'],
            ['id_tipo_usuario' => 3, 'nombre_tipo' => 'CONDUCTOR'],
            ['id_tipo_usuario' => 4, 'nombre_tipo' => 'AUXILIAR EMPRESA'],
            ['id_tipo_usuario' => 5, 'nombre_tipo' => 'PROPIETARIO'],
            ['id_tipo_usuario' => 6, 'nombre_tipo' => 'SETP'],
        ], ['id_tipo_usuario'], ['nombre_tipo']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tipo_usuario')
            ->whereIn('id_tipo_usuario', [1, 2, 3, 4, 5, 6])
            ->delete();
    }
};
