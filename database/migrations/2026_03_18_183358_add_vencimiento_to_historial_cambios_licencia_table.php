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
        Schema::table('historial_cambios_licencia', function (Blueprint $table) {
            $table->date('fecha_vencimiento_anterior')->nullable()->after('fecha_anterior');
            $table->date('fecha_vencimiento_nueva')->nullable()->after('fecha_nueva');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial_cambios_licencia', function (Blueprint $table) {
            //
        });
    }
};
