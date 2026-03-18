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
        Schema::create('historial_cambios_licencia', function (Blueprint $table) {
            $table->id();
            $table->string('doc_conductor', 20);
            $table->date('fecha_anterior');
            $table->date('fecha_nueva');
            $table->string('usuario_modifica', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_cambios_licencia');
    }
};
