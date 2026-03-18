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
        // Elimina la FK antes de modificar la columna
        $table->dropForeign(['id_tarjeta']);
    });

    Schema::table('venta_viaje', function (Blueprint $table) {
        // Ahora puedes modificar la columna
        $table->bigInteger('id_tarjeta')->change();
    });

    // Si necesitas, vuelve a crear la FK después de modificar la columna
    Schema::table('venta_viaje', function (Blueprint $table) {
        $table->foreign('id_tarjeta')->references('id_tarjeta')->on('tarjeta');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venta_viaje', function (Blueprint $table) {
        $table->dropForeign(['id_tarjeta']);
    });

    Schema::table('venta_viaje', function (Blueprint $table) {
        // Revertir el tipo de columna según lo que era antes
        $table->integer('id_tarjeta')->change();
    });

    // Vuelve a crear la FK si era necesaria
    Schema::table('venta_viaje', function (Blueprint $table) {
        $table->foreign('id_tarjeta')->references('id_tarjeta')->on('tarjeta');
    });
    }
};
