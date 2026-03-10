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
        Schema::create('asignacion', function (Blueprint $table) {
            $table->integer('id_asignacion', true);
            $table->tinyInteger('id_tipo_asignacion')->nullable()->index('id_tipo_asignacion');
            $table->string('placa', 15)->nullable()->index('placa');
            $table->unsignedBigInteger('doc_usuario')->nullable()->index('doc_usuario');
            $table->integer('id_ruta')->nullable()->index('id_ruta');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->unsignedTinyInteger('id_estado')->nullable()->index('id_estado');
            $table->bigInteger('Nit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion');
    }
};
