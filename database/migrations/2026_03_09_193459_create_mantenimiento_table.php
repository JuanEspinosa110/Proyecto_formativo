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
        Schema::create('mantenimiento', function (Blueprint $table) {
            $table->integer('id_mantenimiento', true);
            $table->string('placa', 15)->nullable()->index('placa');
            $table->unsignedBigInteger('NIT')->nullable()->index('fk_mantenimiento_empresa');
            $table->integer('kilometraje')->nullable();
            $table->date('fecha_mantenimiento')->nullable();
            $table->date('fecha_proximo')->nullable();
            $table->integer('km_proximo')->nullable();
            $table->decimal('costo_total', 12)->nullable();
            $table->unsignedTinyInteger('id_estado')->nullable()->index('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento');
    }
};
