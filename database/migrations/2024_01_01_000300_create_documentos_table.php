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
        Schema::create('documentos', function (Blueprint $table) {
            $table->bigInteger('id_documento', true);
            $table->string('nombre', 150);
            $table->string('archivo');
            $table->date('fecha_expedicion');
            $table->date('fecha_vencimiento');
            $table->integer('id_tipo_documento')->index('fk_documentos_tipo');
            $table->unsignedBigInteger('doc_usuario')->nullable()->index('fk_documentos_usuario');
            $table->unsignedBigInteger('NIT')->nullable()->index('fk_documentos_empresa');
            $table->string('placa', 15)->nullable()->index('fk_documentos_bus');
            $table->tinyInteger('id_estado');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
