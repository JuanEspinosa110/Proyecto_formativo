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
        Schema::create('tarjeta', function (Blueprint $table) {
            $table->bigInteger('id_tarjeta')->primary();
            $table->decimal('saldo', 10)->nullable();
            $table->string('codigo_tarjeta', 30);
            $table->unsignedTinyInteger('id_estado')->nullable()->index('id_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjeta');
    }
};
