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
        Schema::create('titularidad_tarjeta', function (Blueprint $table) {
            $table->bigIncrements('id_titularidad_tarjeta');
            $table->string('id_tarjeta', 20)->index('tt_id_tarjeta_idx');
            $table->unsignedBigInteger('doc_usuario')->index('tt_doc_usuario_idx');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->unsignedTinyInteger('id_estado')->index('tt_id_estado_idx');
            $table->string('motivo_cambio', 120)->nullable();

            $table->foreign('id_tarjeta', 'tt_fk_tarjeta')
                ->references('id_tarjeta')
                ->on('tarjeta')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('doc_usuario', 'tt_fk_usuario')
                ->references('doc_usuario')
                ->on('usuario')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreign('id_estado', 'tt_fk_estado')
                ->references('id_estado')
                ->on('estado')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titularidad_tarjeta', function (Blueprint $table) {
            $table->dropForeign('tt_fk_tarjeta');
            $table->dropForeign('tt_fk_usuario');
            $table->dropForeign('tt_fk_estado');
        });

        Schema::dropIfExists('titularidad_tarjeta');
    }
};
