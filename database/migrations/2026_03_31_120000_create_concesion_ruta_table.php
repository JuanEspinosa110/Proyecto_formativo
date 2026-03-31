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
        if (!Schema::hasTable('concesion_ruta')) {
            Schema::create('concesion_ruta', function (Blueprint $table) {
                $table->id('id_concesion');
                $table->unsignedBigInteger('NIT');
                $table->integer('id_ruta');
                $table->date('fecha_inicio');
                $table->date('fecha_fin')->nullable();
                $table->integer('id_estado')->default(1);

                // Foreign keys
                $table->foreign('NIT')->references('NIT')->on('empresa')->onDelete('cascade');
                $table->foreign('id_ruta')->references('id_ruta')->on('ruta')->onDelete('cascade');
                $table->foreign('id_estado')->references('id_estado')->on('estado');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concesion_ruta');
    }
};
