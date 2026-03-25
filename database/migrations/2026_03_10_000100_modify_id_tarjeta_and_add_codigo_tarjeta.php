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
        Schema::table('tarjeta', function (Blueprint $table) {
            // Eliminar PK antes de cambiar el tipo (MySQL siempre usa 'PRIMARY' como nombre)
            $table->dropPrimary();
        });

        Schema::table('tarjeta', function (Blueprint $table) {
            // Cambiar id_tarjeta a string (alfanumérico)
            $table->string('id_tarjeta', 20)->change();
        });

        Schema::table('tarjeta', function (Blueprint $table) {
            // Añadir índice único explícito para que las FKs de recarga/venta_viaje funcionen
            // (el change() puede perder el PRIMARY KEY en algunas versiones de MySQL/Doctrine)
            if (!Schema::hasIndex('tarjeta', 'tarjeta_id_tarjeta_unique')) {
                $table->unique('id_tarjeta', 'tarjeta_id_tarjeta_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: la columna contiene datos VARCHAR ('SIGU-777') incompatibles con bigint.
    }
};
