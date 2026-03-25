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
        Schema::create('reportes_fallas', function (Blueprint $table) {
            $table->id('id_reporte');
            $table->string('placa', 15)->index('fk_reportes_fallas_bus');
            $table->unsignedBigInteger('doc_usuario')->index('fk_reportes_fallas_usuario'); // Conductor
            $table->text('descripcion');
            $table->enum('nivel_urgencia', ['Bajo', 'Medio', 'Alto'])->default('Bajo');
            $table->unsignedTinyInteger('id_estado')->nullable()->index('fk_reportes_fallas_estado');
            $table->timestamps();

            // Claves foráneas
            $table->foreign('placa')->references('placa')->on('bus')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('doc_usuario')->references('doc_usuario')->on('usuario')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_estado')->references('id_estado')->on('estado')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_fallas');
    }
};
