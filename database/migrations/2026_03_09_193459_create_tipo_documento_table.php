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
        Schema::create('tipo_documento', function (Blueprint $table) {
            $table->integer('id_tipo_documento', true);
            $table->string('nombre', 100);
            $table->string('descripcion')->nullable();
            $table->boolean('requiere_doc_usuario')->nullable()->default(false);
            $table->boolean('requiere_placa')->nullable()->default(false);
            $table->tinyInteger('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_documento');
    }
};
