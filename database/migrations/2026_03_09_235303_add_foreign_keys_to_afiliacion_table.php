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
          Schema::table('afiliacion', function (Blueprint $table) {
            $table->foreign('placa')
                ->references('placa')->on('bus')
                ->onDelete('restrict');
            $table->foreign('NIT')
                ->references('NIT')->on('empresa')
                ->onDelete('restrict');
            $table->foreign('id_estado')
                ->references('id_estado')->on('estado')
                ->onDelete('restrict');
          });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliacion', function (Blueprint $table) {
            $table->dropForeign(['placa']);
            $table->dropForeign(['NIT']);
            $table->dropForeign(['id_estado']);
        });
    }
};
