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
        Schema::table('detalle_mantenimiento', function (Blueprint $table) {
            $table->foreign(['id_mantenimiento'], 'detalle_mantenimiento_ibfk_1')->references(['id_mantenimiento'])->on('mantenimiento')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_tipo_mantenimiento'], 'detalle_mantenimiento_ibfk_2')->references(['id_tipo_mantenimiento'])->on('tipo_mantenimiento')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_mantenimiento', function (Blueprint $table) {
            $table->dropForeign('detalle_mantenimiento_ibfk_1');
            $table->dropForeign('detalle_mantenimiento_ibfk_2');
        });
    }
};
