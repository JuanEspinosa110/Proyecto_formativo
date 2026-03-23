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
        // Añadir el nuevo tipo de empresa
        DB::table('tipo_empresa')->insertOrIgnore([
            'id_tipo_empresa' => 6,
            'nombre_tipo' => 'EMPRESA DE RECARGAS'
        ]);

        // Añadir el nuevo tipo de usuario (10: GESTOR RECARGAS)
        DB::table('tipo_usuario')->insertOrIgnore([
            'id_tipo_usuario' => 10,
            'nombre_tipo' => 'GESTOR RECARGAS'
        ]);

        // Añadir columna doc_usuario_gestor a la tabla recarga
        Schema::table('recarga', function (Blueprint $table) {
            if (!Schema::hasColumn('recarga', 'doc_usuario_gestor')) {
                $table->unsignedBigInteger('doc_usuario_gestor')->nullable()->after('monto');
                $table->foreign('doc_usuario_gestor')->references('doc_usuario')->on('usuario')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recarga', function (Blueprint $table) {
            if (Schema::hasColumn('recarga', 'doc_usuario_gestor')) {
                $table->dropForeign(['doc_usuario_gestor']);
                $table->dropColumn('doc_usuario_gestor');
            }
        });

        DB::table('tipo_usuario')->where('id_tipo_usuario', 10)->delete();
        DB::table('tipo_empresa')->where('id_tipo_empresa', 6)->delete();
    }
};
