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
            $columnsToDrop = ['placa', 'id_ruta', 'doc_us', 'cantidad_pasajeros', 'ingresos'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('recorridos', $column)) {
                    $table->dropColumn($column);
                }
            }
            $table->unsignedBigInteger('id_viaje')->nullable()->after('id_recorrido');
            $table->enum('sentido', ['IDA', 'VUELTA'])->nullable()->after('id_viaje');
            $table->string('foto_torniquete')->nullable()->after('hora_llegada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recorridos', function (Blueprint $table) {
            $table->string('placa', 20)->nullable();
            $table->unsignedBigInteger('id_ruta')->nullable();
            $table->unsignedBigInteger('doc_us')->nullable();
            $table->integer('cantidad_pasajeros')->default(0);
            $table->decimal('ingresos', 12, 2)->default(0);
            
            $table->dropColumn(['id_viaje', 'foto_torniquete', 'sentido']);
        });
    }
};
