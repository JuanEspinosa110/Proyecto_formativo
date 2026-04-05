<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('super_administrador', function (Blueprint $table) {
            $table->char('id_ciudad', 6)->nullable()->after('id_estado');
            $table->foreign('id_ciudad')->references('id_ciudad')->on('ciudad');
        });

        // Set default value for existing super administrators
        DB::table('super_administrador')->update(['id_ciudad' => '730001']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('super_administrador', function (Blueprint $table) {
            $table->dropForeign(['id_ciudad']);
            $table->dropColumn('id_ciudad');
        });
    }
};
