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
        Schema::table('tarjeta', function (Blueprint $table) {
            $table->unsignedBigInteger('doc_usuario'); // bigint(20) obligatorio
            $table->foreign('doc_usuario')
                  ->references('doc_usuario')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarjeta', function (Blueprint $table) {
            $table->dropForeign(['doc_usuario']);
            $table->dropColumn('doc_usuario');
        });
    }
};
