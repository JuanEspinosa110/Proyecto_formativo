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
        Schema::create('novedad_recorridos', function (Blueprint $table) {
            $table->id('id_novedad');

            $table->unsignedBigInteger('id_recorrido');
            $table->foreign('id_recorrido')->references('id_recorrido')->on('recorridos')->onDelete('cascade');

            $table->unsignedBigInteger('doc_controlador');
            $table->foreign('doc_controlador')->references('doc_usuario')->on('usuario');

            $table->enum('tipo', ['CHECKPOINT', 'INCIDENCIA'])->default('CHECKPOINT');
            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('novedad_recorridos');
    }
};
