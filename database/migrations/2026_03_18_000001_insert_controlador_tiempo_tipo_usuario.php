<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tipo_usuario')->upsert(
            [['id_tipo_usuario' => 8, 'nombre_tipo' => 'CONTROLADOR_TIEMPO']],
            ['id_tipo_usuario'],
            ['nombre_tipo']
        );
    }

    public function down(): void
    {
        DB::table('tipo_usuario')->where('id_tipo_usuario', 8)->delete();
    }
};
