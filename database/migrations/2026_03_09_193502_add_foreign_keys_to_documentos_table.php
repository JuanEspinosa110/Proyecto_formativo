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
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreign(['NIT'], 'documentos_ibfk_1')->references(['NIT'])->on('empresa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['placa'], 'fk_documentos_bus')->references(['placa'])->on('bus')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['id_tipo_documento'], 'fk_documentos_tipo')->references(['id_tipo_documento'])->on('tipo_documento')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['doc_usuario'], 'fk_documentos_usuario')->references(['doc_usuario'])->on('usuario')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign('documentos_ibfk_1');
            $table->dropForeign('fk_documentos_bus');
            $table->dropForeign('fk_documentos_tipo');
            $table->dropForeign('fk_documentos_usuario');
        });
    }
};
