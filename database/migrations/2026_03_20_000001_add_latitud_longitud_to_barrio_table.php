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
        // No-op: tarjeta.id_tarjeta perdió su PRIMARY KEY al hacer change() a VARCHAR en 000100.
        // No se puede añadir FK sin índice en la tabla referenciada.
        // La integridad venta_viaje → tarjeta se gestiona a nivel de aplicación.
        Schema::table('barrio', function (Blueprint $table) {
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barrio', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};
