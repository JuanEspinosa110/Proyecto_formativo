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
        // 1. Standardize NIT type to unsignedBigInteger across all tables
        // We use change() which requires doctrine/dbal, but Laravel 10+ handles it natively
        Schema::table('empresa', function (Blueprint $table) {
            $table->unsignedBigInteger('NIT')->change();
        });

        Schema::table('bus', function (Blueprint $table) {
            $table->unsignedBigInteger('NIT')->nullable()->change();
        });

        Schema::table('documentos', function (Blueprint $table) {
            $table->unsignedBigInteger('NIT')->nullable()->change();
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->unsignedBigInteger('NIT')->nullable()->change();
        });

        // 2. Add NIT to ruta (Refactor request)
        Schema::table('ruta', function (Blueprint $table) {
            if (!Schema::hasColumn('ruta', 'NIT')) {
                $table->unsignedBigInteger('NIT')->nullable()->after('id_ruta');
            }
        });

        // 3. Cleaning up orphaned records to avoid FK violations
        DB::statement("DELETE FROM bus WHERE NIT IS NOT NULL AND NIT NOT IN (SELECT NIT FROM empresa)");
        DB::statement("DELETE FROM ruta WHERE NIT IS NOT NULL AND NIT NOT IN (SELECT NIT FROM empresa)");
        DB::statement("DELETE FROM viaje WHERE placa IS NOT NULL AND placa NOT IN (SELECT placa FROM bus)");
        DB::statement("DELETE FROM viaje WHERE id_ruta IS NOT NULL AND id_ruta NOT IN (SELECT id_ruta FROM ruta)");

        // 4. Force CASCADE on existing and new relations
        Schema::table('bus', function (Blueprint $table) {
            // Drop old baseline constraints if they exist
            try { DB::statement("ALTER TABLE bus DROP FOREIGN KEY bus_fk_empresa"); } catch(\Exception $e) {}
            try { DB::statement("ALTER TABLE bus DROP FOREIGN KEY bus_fk_estado"); } catch(\Exception $e) {}
            
            $table->foreign('NIT', 'fk_bus_empresa')->references('NIT')->on('empresa')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estado', 'fk_bus_estado')->references('id_estado')->on('estado')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('ruta', function (Blueprint $table) {
            try { DB::statement("ALTER TABLE ruta DROP FOREIGN KEY ruta_fk_estado"); } catch(\Exception $e) {}
            // Note: ruta_fk_empresa doesn't exist yet in baseline as we added NIT column just now
            
            $table->foreign('NIT', 'fk_ruta_empresa')->references('NIT')->on('empresa')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_estado', 'fk_ruta_estado')->references('id_estado')->on('estado')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('viaje', function (Blueprint $table) {
            try { DB::statement("ALTER TABLE viaje DROP FOREIGN KEY viaje_fk_bus"); } catch(\Exception $e) {}
            try { DB::statement("ALTER TABLE viaje DROP FOREIGN KEY viaje_fk_ruta"); } catch(\Exception $e) {}
            
            $table->foreign('placa', 'fk_viaje_bus')->references('placa')->on('bus')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_ruta', 'fk_viaje_ruta')->references('id_ruta')->on('ruta')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('documentos', function (Blueprint $table) {
            try { DB::statement("ALTER TABLE documentos DROP FOREIGN KEY doc_fk_empresa"); } catch(\Exception $e) {}
            $table->foreign('NIT', 'fk_documentos_empresa')->references('NIT')->on('empresa')->onDelete('cascade')->onUpdate('cascade');
        });
        
        Schema::table('usuario', function (Blueprint $table) {
            try { DB::statement("ALTER TABLE usuario DROP FOREIGN KEY usuario_fk_empresa"); } catch(\Exception $e) {}
            $table->foreign('NIT', 'fk_usuario_empresa')->references('NIT')->on('empresa')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal logic if needed (usually not during this refactor phase)
    }
};
