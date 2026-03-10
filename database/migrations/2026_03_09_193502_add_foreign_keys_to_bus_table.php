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
        Schema::table('bus', function (Blueprint $table) {
            $table->foreign(['id_estado'], 'bus_ibfk_2')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['NIT'], 'bus_ibfk_3')->references(['NIT'])->on('empresa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus', function (Blueprint $table) {
            $table->dropForeign('bus_ibfk_2');
            $table->dropForeign('bus_ibfk_3');
        });
    }
};
