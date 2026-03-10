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
        Schema::create('usuario', function (Blueprint $table) {
            $table->unsignedBigInteger('doc_usuario')->primary();
            $table->unsignedBigInteger('NIT')->nullable()->index('usuario_ibfk_1');
            $table->string('primer_nombre', 50)->nullable();
            $table->string('segundo_nombre', 50)->nullable();
            $table->string('primer_apellido', 50)->nullable();
            $table->string('segundo_apellido', 50)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('password')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('foto_usuario', 300)->nullable();
            $table->tinyInteger('id_tipo_usuario')->nullable()->index('id_tipo_usuario');
            $table->char('id_ciudad', 6)->nullable()->index('id_ciudad');
            $table->unsignedTinyInteger('id_estado')->nullable()->index('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
