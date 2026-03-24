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
        Schema::create('ruta', function (Blueprint $table) {
            $table->integer('id_ruta', true);
            $table->char('id_ciudad', 6)->index('id_ciudad');
            $table->string('codigo_ruta', 50); // Cambiado a string
            $table->integer('id_barrio_origen')->index('fk_ruta_barrio_origen');
            $table->integer('id_barrio_destino')->index('fk_ruta_barrio_destino');
            $table->unsignedTinyInteger('id_estado')->index('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruta');
    }
};
