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
        Schema::create('empresa', function (Blueprint $table) {
            $table->unsignedBigInteger('NIT')->primary();
            $table->string('nombre_empresa', 150);
            $table->unsignedBigInteger('doc_representante');
            $table->string('primer_nombre_repre', 50);
            $table->string('segundo_nombre_repre', 50)->nullable();
            $table->string('primer_apellido_repre', 50);
            $table->string('segundo_apellido_repre', 50)->nullable();
            $table->string('telefono_representante', 20)->nullable();
            $table->string('correo_representante', 150)->nullable();
            $table->string('telefono_empresa', 20)->nullable();
            $table->string('correo_corporativo', 150)->nullable();
            $table->tinyInteger('id_tipo_empresa')->index('empresa_ibfk_3');
            $table->char('id_ciudad', 6)->nullable()->index('id_ciudad');
            $table->unsignedTinyInteger('id_estado')->nullable()->index('id_estado');
            $table->dateTime('fecha_creacion')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};
