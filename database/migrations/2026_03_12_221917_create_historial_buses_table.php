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
        Schema::create('historial_buses', function (Blueprint $table) {
            $table->id('id_historial');
            $table->string('placa', 15)->index();
            $table->integer('id_ruta')->nullable()->index();
            $table->bigInteger('doc_us')->unsigned()->nullable()->index();
            $table->string('tipo_cambio');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_buses');
    }
};
