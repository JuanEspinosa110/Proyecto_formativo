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
        Schema::table('empresa', function (Blueprint $table) {
            $table->foreign(['id_estado'], 'empresa_ibfk_2')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_tipo_empresa'], 'empresa_ibfk_3')->references(['id_tipo_empresa'])->on('tipo_empresa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ciudad'], 'empresa_ibfk_4')->references(['id_ciudad'])->on('ciudad')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropForeign('empresa_ibfk_2');
            $table->dropForeign('empresa_ibfk_3');
            $table->dropForeign('empresa_ibfk_4');
        });
    }
};
