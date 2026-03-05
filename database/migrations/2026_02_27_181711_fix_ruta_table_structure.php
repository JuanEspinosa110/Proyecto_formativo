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
        // Asegurar que id_ruta NO sea auto_increment (diseño por ID aleatorio)
        try {
            DB::statement("ALTER TABLE `ruta` MODIFY `id_ruta` INT(11) NOT NULL");
        } catch (\Exception $e) {}

        Schema::table('ruta', function (Blueprint $table) {
            // Drop redundant/obsolete columns if they exist
            if (Schema::hasColumn('ruta', 'NIT')) {
                // Drop foreign key first if it exists (check name from previous refactor)
                try {
                    $table->dropForeign('fk_ruta_empresa');
                } catch (\Exception $e) {}
                $table->dropColumn('NIT');
            }
            if (Schema::hasColumn('ruta', 'origen')) {
                $table->dropColumn('origen');
            }
            if (Schema::hasColumn('ruta', 'destino')) {
                $table->dropColumn('destino');
            }

            // Enforce types and lengths for the remaining official columns
            $table->char('id_ciudad', 6)->change();
            $table->integer('id_barrio_origen')->change();
            $table->integer('id_barrio_destino')->change();
            $table->unsignedTinyInteger('id_estado')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
