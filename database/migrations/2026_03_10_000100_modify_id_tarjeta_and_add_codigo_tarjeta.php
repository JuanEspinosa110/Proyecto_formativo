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
            // Cambiar id_tarjeta a string (alfanumérico)
            $table->string('id_tarjeta', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarjeta', function (Blueprint $table) {
            // Revertir id_tarjeta a bigint (o el tipo original)
            $table->bigInteger('id_tarjeta')->change();
        });
    }
};
