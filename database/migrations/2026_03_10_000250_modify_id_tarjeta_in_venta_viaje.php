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
        // No-op: La FK id_tarjeta ya fue eliminada por 2026_03_10_000002
        // y la columna ya es bigInteger por 2026_03_10_000100.
        // Esta migración quedó redundante.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: no es posible revertir bigint→int con datos VARCHAR (Ej: 'SIGU-777') en la columna.
        // La estructura actual es la correcta y no debe revertirse.
    }
};
