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
        Schema::create('venta_viaje', function (Blueprint $table) {
            $table->integer('id_venta', true);
            $table->integer('id_viaje')->nullable()->index('id_viaje');
            $table->string('id_tarjeta', 20)->nullable();
            $table->decimal('valor', 10)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->unsignedTinyInteger('id_estado')->nullable()->index('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_viaje');
    }
};
