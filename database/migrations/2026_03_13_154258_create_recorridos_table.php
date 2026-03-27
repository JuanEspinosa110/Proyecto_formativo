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
        Schema::dropIfExists('recorridos');
        Schema::create('recorridos', function (Blueprint $table) {
            $table->id('id_recorrido');
            $table->string('placa', 15)->nullable()->index();
            $table->integer('id_ruta')->nullable()->index();
            $table->unsignedBigInteger('doc_us')->nullable()->index();
            $table->dateTime('hora_salida')->nullable();
            $table->dateTime('hora_llegada')->nullable();
            $table->integer('cantidad_pasajeros')->default(0);
            $table->decimal('ingresos', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recorridos');
    }
};
