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
        Schema::table('detalle_mantenimiento', function (Blueprint $table) {
            $table->unsignedBigInteger('id_reporte')->nullable()->after('descripcion');

            if (Schema::hasTable('reportes_fallas')) {
                $table->foreign('id_reporte')
                      ->references('id_reporte')
                      ->on('reportes_fallas')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detalle_mantenimiento', function (Blueprint $table) {
            $table->dropForeign(['id_reporte']);
            $table->dropColumn('id_reporte');
        });
    }
};
