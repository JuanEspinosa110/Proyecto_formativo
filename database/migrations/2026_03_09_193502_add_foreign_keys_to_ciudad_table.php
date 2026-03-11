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
        Schema::table('ciudad', function (Blueprint $table) {
            $table->foreign(['id_departamento'], 'ciudad_ibfk_1')->references(['id_departamento'])->on('departamento')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ciudad', function (Blueprint $table) {
            $table->dropForeign('ciudad_ibfk_1');
        });
    }
};
