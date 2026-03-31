<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tarjeta', function (Blueprint $table) {
            // Eliminar la FK si existe
            if (Schema::hasColumn('tarjeta', 'doc_usuario')) {
                $table->dropForeign(['doc_usuario']);
                $table->dropColumn('doc_usuario');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarjeta', function (Blueprint $table) {
            $table->unsignedBigInteger('doc_usuario')->nullable()->after('id_estado');
            $table->foreign('doc_usuario')->references('doc_usuario')->on('usuario')->nullOnDelete();
        });
    }
};
