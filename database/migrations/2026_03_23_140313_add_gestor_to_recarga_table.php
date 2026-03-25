<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Stub de migración restaurada.
 * El archivo original fue eliminado del disco pero quedó registrado en la BD.
 * Esta versión stub permite que migrate:refresh funcione correctamente.
 * La tabla `recarga` ya tiene la estructura final esperada; up/down son no-ops.
 */
return new class extends Migration
{
    public function up(): void
    {
        // No-op: la tabla recarga ya tiene la estructura correcta.
    }

    public function down(): void
    {
        // No-op: no hay nada que revertir.
    }
};
