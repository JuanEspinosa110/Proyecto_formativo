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
        // 1. Seed tipo_asignacion table
        DB::table('tipo_asignacion')->insertOrIgnore([
            ['id_tipo_asignacion' => 1, 'nombre_tipo' => 'ASIGNACION_DIRECTA'],
            ['id_tipo_asignacion' => 3, 'nombre_tipo' => 'ASIGNACION_RUTA'],
            ['id_tipo_asignacion' => 4, 'nombre_tipo' => 'HISTORIAL_VIAJE'],
        ]);

        // 2. Create Trigger AFTER_VIAJE_INSERT
        DB::unprepared("
            CREATE TRIGGER after_viaje_insert
            AFTER INSERT ON viaje
            FOR EACH ROW
            BEGIN
                DECLARE v_nit BIGINT;
                
                -- Obtener el NIT de la empresa dueña del bus
                SELECT NIT INTO v_nit FROM bus WHERE placa = NEW.placa LIMIT 1;
                
                -- Insertar en la tabla asignacion como historial
                INSERT INTO asignacion (
                    id_tipo_asignacion,
                    placa,
                    doc_usuario,
                    id_ruta,
                    fecha_inicio,
                    id_estado,
                    Nit
                ) VALUES (
                    4, 
                    NEW.placa, 
                    NEW.doc_us, 
                    NEW.id_ruta, 
                    DATE(NEW.fecha), 
                    NEW.id_estado, 
                    v_nit
                );
            END
        ");

        // 3. Create Trigger AFTER_VIAJE_UPDATE (to keep status in sync)
        DB::unprepared("
            CREATE TRIGGER after_viaje_update
            AFTER UPDATE ON viaje
            FOR EACH ROW
            BEGIN
                -- Actualizar el estado en el historial si cambia en el viaje activo
                -- Esto permite saber si un viaje fue cancelado o terminado
                UPDATE asignacion 
                SET id_estado = NEW.id_estado,
                    id_ruta = NEW.id_ruta,
                    doc_usuario = NEW.doc_us
                WHERE placa = NEW.placa 
                  AND id_tipo_asignacion = 4 
                  AND DATE(fecha_inicio) = DATE(NEW.fecha);
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_viaje_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_viaje_update');
        
        DB::table('tipo_asignacion')->whereIn('id_tipo_asignacion', [1, 3, 4])->delete();
    }
};
