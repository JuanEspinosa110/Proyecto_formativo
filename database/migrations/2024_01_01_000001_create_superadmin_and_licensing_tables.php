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
        // 1. Super Administrador
        Schema::create('super_administrador', function (Blueprint $table) {
            $table->unsignedBigInteger('doc_super_admin')->primary();
            $table->string('nombre', 100);
            $table->string('correo', 150)->unique();
            $table->string('telefono', 20);
            $table->string('foto_perfil', 255)->nullable();
            $table->string('password', 255);
            $table->unsignedTinyInteger('id_estado')->default(1);
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('id_estado')->references('id_estado')->on('estado');
        });

        // 2. Planes de Licencia
        Schema::create('planes_licencia', function (Blueprint $table) {
            $table->integer('id_plan')->autoIncrement();
            $table->string('nombre_plan', 50)->unique();
            $table->integer('duracion_meses');
            $table->decimal('precio', 12, 2);
            $table->text('descripcion');
            $table->unsignedTinyInteger('id_estado')->default(1);
            
            $table->foreign('id_estado')->references('id_estado')->on('estado');
        });

        // 3. Licencias
        Schema::create('licencias', function (Blueprint $table) {
            $table->string('id_licencia', 20)->primary();
            $table->unsignedBigInteger('NIT');
            $table->integer('id_plan');
            $table->date('fecha_inicio');
            $table->date('fecha_vencimiento');
            $table->unsignedTinyInteger('id_estado')->default(1);
            $table->unsignedBigInteger('doc_super_admin')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->foreign('NIT')->references('NIT')->on('empresa')->onDelete('cascade');
            $table->foreign('id_plan')->references('id_plan')->on('planes_licencia');
            $table->foreign('id_estado')->references('id_estado')->on('estado');
            $table->foreign('doc_super_admin')->references('doc_super_admin')->on('super_administrador');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencias');
        Schema::dropIfExists('planes_licencia');
        Schema::dropIfExists('super_administrador');
    }
};
