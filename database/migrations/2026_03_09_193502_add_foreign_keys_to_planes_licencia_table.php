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
        Schema::table('planes_licencia', function (Blueprint $table) {
            $table->foreign(['id_estado'], 'planes_licencia_ibfk_1')->references(['id_estado'])->on('estado')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planes_licencia', function (Blueprint $table) {
            $table->dropForeign('planes_licencia_ibfk_1');
        });
    }
};
