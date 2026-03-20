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
        // Eliminado: la FK de id_tarjeta ya no existe ni se elimina aquí
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminado: la FK de id_tarjeta ya no existe ni se crea aquí
    }
};
