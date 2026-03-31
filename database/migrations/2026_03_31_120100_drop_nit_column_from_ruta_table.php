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
        if (Schema::hasColumn('ruta', 'NIT')) {
            Schema::table('ruta', function (Blueprint $table) {
                // Drop foreign key first
                try {
                    $table->dropForeign('fk_ruta_empresa');
                } catch (\Exception $e) {
                    // If it doesn't exist, ignore
                }
                $table->dropColumn('NIT');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruta', function (Blueprint $table) {
            $table->unsignedBigInteger('NIT')->nullable();
            $table->foreign('NIT')->references('NIT')->on('empresa')->onDelete('cascade');
        });
    }
};
