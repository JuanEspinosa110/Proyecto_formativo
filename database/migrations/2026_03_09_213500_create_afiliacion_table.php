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
        Schema::create('afiliacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('placa', 15); // FK a bus.placa
            $table->unsignedBigInteger('NIT'); // FK a empresa.NIT
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->unsignedTinyInteger('id_estado'); // FK a estado.id_estado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afiliacion');
    }
};
