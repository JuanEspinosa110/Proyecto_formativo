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
        Schema::table('recarga', function (Blueprint $table) {
            $table->string('id_tarjeta', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: la columna id_tarjeta contiene datos VARCHAR (Ej: 'TARJ-INT-10001')
        // que no son compatibles con bigInteger. No se puede revertir sin perder datos.
    }
};
