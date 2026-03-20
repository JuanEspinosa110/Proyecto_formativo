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
        Schema::table('venta_viaje', function (Blueprint $table) {
            $table->foreign(['id_viaje'], 'venta_viaje_ibfk_1')->references(['id_viaje'])->on('viaje')->onUpdate('restrict')->onDelete('restrict');
            // Eliminado: la FK de id_tarjeta se crea en la migración de creación
            $table->foreign(['id_estado'], 'venta_viaje_ibfk_3')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venta_viaje', function (Blueprint $table) {
            $table->dropForeign('venta_viaje_ibfk_1');
            // Eliminado: la FK de id_tarjeta ya no existe ni se elimina aquí
            $table->dropForeign('venta_viaje_ibfk_3');
        });
    }
};
