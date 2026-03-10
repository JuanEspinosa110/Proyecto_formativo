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
        Schema::table('licencias', function (Blueprint $table) {
            $table->foreign(['id_plan'], 'licencias_ibfk_2')->references(['id_plan'])->on('planes_licencia')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_estado'], 'licencias_ibfk_3')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['doc_super_admin'], 'licencias_ibfk_4')->references(['doc_super_admin'])->on('super_administrador')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['NIT'], 'licencias_ibfk_5')->references(['NIT'])->on('empresa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licencias', function (Blueprint $table) {
            $table->dropForeign('licencias_ibfk_2');
            $table->dropForeign('licencias_ibfk_3');
            $table->dropForeign('licencias_ibfk_4');
            $table->dropForeign('licencias_ibfk_5');
        });
    }
};
