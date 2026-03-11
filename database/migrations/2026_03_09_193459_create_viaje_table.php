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
        Schema::create('viaje', function (Blueprint $table) {
            $table->integer('id_viaje')->primary();
            $table->string('placa', 15)->nullable()->index('placa');
            $table->integer('id_ruta')->nullable()->index('id_ruta');
            $table->unsignedBigInteger('doc_us')->nullable()->index('doc_us');
            $table->dateTime('fecha')->nullable();
            $table->unsignedTinyInteger('id_estado')->nullable()->index('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viaje');
    }
};
