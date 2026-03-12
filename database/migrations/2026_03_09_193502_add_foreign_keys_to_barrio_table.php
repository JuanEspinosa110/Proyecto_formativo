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
        Schema::table('barrio', function (Blueprint $table) {
            $table->foreign(['id_ciudad'], 'barrio_ibfk_1')->references(['id_ciudad'])->on('ciudad')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barrio', function (Blueprint $table) {
            $table->dropForeign('barrio_ibfk_1');
        });
    }
};
