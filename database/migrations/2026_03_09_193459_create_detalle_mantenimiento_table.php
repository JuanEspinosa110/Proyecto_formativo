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
        Schema::create('detalle_mantenimiento', function (Blueprint $table) {
            $table->integer('id_detalle', true);
            $table->integer('id_mantenimiento')->nullable()->index('id_mantenimiento');
            $table->integer('id_tipo_mantenimiento')->nullable()->index('id_tipo_mantenimiento');
            $table->text('descripcion')->nullable();
            $table->string('evidencia_foto', 300)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_mantenimiento');
    }
};
