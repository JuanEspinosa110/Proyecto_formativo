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
        Schema::table('recorridos', function (Blueprint $table) {
            if (!Schema::hasColumn('recorridos', 'sentido')) {
                $table->enum('sentido', ['IDA', 'VUELTA'])->nullable()->after('id_recorrido');
            }
            if (!Schema::hasColumn('recorridos', 'foto_torniquete')) {
                $table->string('foto_torniquete')->nullable()->after('hora_llegada');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recorridos', function (Blueprint $table) {
            $table->dropColumn(['sentido', 'foto_torniquete']);
        });
    }
};
