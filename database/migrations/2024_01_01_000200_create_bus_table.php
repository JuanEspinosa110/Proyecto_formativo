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
        Schema::create('bus', function (Blueprint $table) {
            $table->string('placa', 15)->primary();
            $table->unsignedBigInteger('NIT')->index('nit');
            $table->string('modelo', 50)->nullable();
            $table->integer('capacidad_pasajeros')->nullable();
            $table->integer('kilometraje')->nullable();
            $table->unsignedTinyInteger('id_estado')->nullable()->index('id_estado');
            $table->bigInteger('linc_transito');
            $table->string('numero_chasis', 17);
            $table->string('numero_motor', 14);
            $table->bigInteger('doc_propietario');
            $table->string('nombre_propietario', 50);
            $table->string('telefono', 20);
            $table->string('correo', 150);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus');
    }
};
