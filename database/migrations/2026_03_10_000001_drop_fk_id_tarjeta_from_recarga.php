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
        Schema::table('recarga', function (Blueprint $table) {
            $table->dropForeign('recarga_ibfk_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recarga', function (Blueprint $table) {
            $table->foreign('id_tarjeta')
                  ->references('id_tarjeta')->on('tarjeta')
                  ->onDelete('restrict');
        });
    }
};
