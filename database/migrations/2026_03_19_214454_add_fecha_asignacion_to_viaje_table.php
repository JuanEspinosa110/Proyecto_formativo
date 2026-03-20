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
        Schema::table('viaje', function (Blueprint $table) {
            $table->dateTime('fecha_asignacion')->nullable()->after('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viaje', function (Blueprint $table) {
            $table->dropColumn('fecha_asignacion');
        });
    }
};
