<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asignacion', function (Blueprint $table) {
            $table->foreign(['id_tipo_asignacion'], 'asignacion_ibfk_1')->references(['id_tipo_asignacion'])->on('tipo_asignacion')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['placa'], 'asignacion_ibfk_2')->references(['placa'])->on('bus')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['doc_usuario'], 'asignacion_ibfk_3')->references(['doc_usuario'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ruta'], 'asignacion_ibfk_4')->references(['id_ruta'])->on('ruta')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_estado'], 'asignacion_ibfk_5')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignacion', function (Blueprint $table) {
            $table->dropForeign('asignacion_ibfk_1');
            $table->dropForeign('asignacion_ibfk_2');
            $table->dropForeign('asignacion_ibfk_3');
            $table->dropForeign('asignacion_ibfk_4');
            $table->dropForeign('asignacion_ibfk_5');
        });
    }
};
