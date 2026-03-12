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
        Schema::table('mantenimiento', function (Blueprint $table) {
            $table->foreign(['placa'], 'mantenimiento_ibfk_1')->references(['placa'])->on('bus')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_estado'], 'mantenimiento_ibfk_2')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['NIT'], 'mantenimiento_ibfk_3')->references(['NIT'])->on('empresa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimiento', function (Blueprint $table) {
            $table->dropForeign('mantenimiento_ibfk_1');
            $table->dropForeign('mantenimiento_ibfk_2');
            $table->dropForeign('mantenimiento_ibfk_3');
        });
    }
};
