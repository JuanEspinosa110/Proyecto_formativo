<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renombrar ID 7 (anteriormente controlador_tiempo o similar)
        DB::table('tipo_usuario')->where('id_tipo_usuario', 7)->update(['nombre_tipo' => 'COORDINADOR BUS']);
        
        // Asegurar ID 8 (anteriormente controlador_tiempo en una migración conflictiva)
        DB::table('tipo_usuario')->where('id_tipo_usuario', 8)->update(['nombre_tipo' => 'GESTOR DE RECARGAS']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tipo_usuario')->where('id_tipo_usuario', 7)->update(['nombre_tipo' => 'CONTROLADOR_TIEMPO']);
    }
};
