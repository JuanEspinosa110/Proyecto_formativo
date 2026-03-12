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
        Schema::table('ruta', function (Blueprint $table) {
            $table->foreign(['id_barrio_destino'], 'fk_ruta_barrio_destino')->references(['id_barrio'])->on('barrio')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['id_barrio_origen'], 'fk_ruta_barrio_origen')->references(['id_barrio'])->on('barrio')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['id_estado'], 'ruta_ibfk_1')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ciudad'], 'ruta_ibfk_2')->references(['id_ciudad'])->on('ciudad')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruta', function (Blueprint $table) {
            $table->dropForeign('fk_ruta_barrio_destino');
            $table->dropForeign('fk_ruta_barrio_origen');
            $table->dropForeign('ruta_ibfk_1');
            $table->dropForeign('ruta_ibfk_2');
        });
    }
};
