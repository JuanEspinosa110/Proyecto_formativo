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
        Schema::table('usuario', function (Blueprint $table) {
            $table->foreign(['id_tipo_usuario'], 'usuario_ibfk_2')->references(['id_tipo_usuario'])->on('tipo_usuario')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_estado'], 'usuario_ibfk_4')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['NIT'], 'usuario_ibfk_5')->references(['NIT'])->on('empresa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ciudad'], 'usuario_ibfk_6')->references(['id_ciudad'])->on('ciudad')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropForeign('usuario_ibfk_2');
            $table->dropForeign('usuario_ibfk_4');
            $table->dropForeign('usuario_ibfk_5');
            $table->dropForeign('usuario_ibfk_6');
        });
    }
};
