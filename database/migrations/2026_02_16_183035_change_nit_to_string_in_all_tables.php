<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1️⃣ Eliminar foreign keys
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign('fk_documentos_empresa');
        });

        Schema::table('mantenimiento', function (Blueprint $table) {
            $table->dropForeign('fk_mantenimiento_empresa');
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->dropForeign('usuario_ibfk_1');
        });

        // 2️⃣ Cambiar tipo en tabla principal
        Schema::table('empresa', function (Blueprint $table) {
            $table->string('NIT', 20)->change();
        });

        // 3️⃣ Cambiar tipo en tablas hijas
        Schema::table('documentos', function (Blueprint $table) {
            $table->string('NIT', 20)->change();
        });

        Schema::table('mantenimiento', function (Blueprint $table) {
            $table->string('NIT', 20)->change();
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->string('NIT', 20)->nullable()->change();

        });

        // 4️⃣ Volver a crear foreign keys
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreign('NIT')
                  ->references('NIT')
                  ->on('empresa')
                  ->onDelete('cascade');
        });

        Schema::table('mantenimiento', function (Blueprint $table) {
            $table->foreign('NIT')
                  ->references('NIT')
                  ->on('empresa')
                  ->onDelete('cascade');
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->foreign('NIT')
                  ->references('NIT')
                  ->on('empresa')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        // Opcional: revertir cambios
    }
};
