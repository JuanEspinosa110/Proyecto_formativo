<?php

namespace App\Services;

use App\Models\Tarjeta;
use App\Models\TitularidadTarjeta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TitularidadTarjetaService
{
    private const ESTADO_ACTIVO = 1;
    private const ESTADO_INACTIVO = 2;

    /**
     * Cambia el titular de una tarjeta cerrando la titularidad activa anterior.
     */
    public function cambiarTitular(
        string $idTarjeta,
        int $nuevoDocUsuario,
        ?string $motivoCambio = null,
        ?string $fechaCambio = null
    ): TitularidadTarjeta {
        $fecha = $fechaCambio ?? Carbon::now()->toDateString();

        return DB::transaction(function () use ($idTarjeta, $nuevoDocUsuario, $motivoCambio, $fecha) {
            $tarjeta = Tarjeta::query()
                ->where('id_tarjeta', $idTarjeta)
                ->lockForUpdate()
                ->firstOrFail();

            $titularidadActiva = TitularidadTarjeta::query()
                ->where('id_tarjeta', $idTarjeta)
                ->where('id_estado', self::ESTADO_ACTIVO)
                ->whereNull('fecha_fin')
                ->lockForUpdate()
                ->first();

            if ($titularidadActiva && (int) $titularidadActiva->doc_usuario === $nuevoDocUsuario) {
                return $titularidadActiva;
            }

            if ($titularidadActiva) {
                $titularidadActiva->update([
                    'fecha_fin' => $fecha,
                    'id_estado' => self::ESTADO_INACTIVO,
                    'motivo_cambio' => $motivoCambio,
                ]);
            }

            $nuevaTitularidad = TitularidadTarjeta::query()->create([
                'id_tarjeta' => $idTarjeta,
                'doc_usuario' => $nuevoDocUsuario,
                'fecha_inicio' => $fecha,
                'fecha_fin' => null,
                'id_estado' => self::ESTADO_ACTIVO,
                'motivo_cambio' => $motivoCambio,
            ]);

            $tarjeta->update(['doc_usuario' => $nuevoDocUsuario]);

            return $nuevaTitularidad;
        });
    }

    /**
     * Registra una titularidad inicial solo si no existe una activa.
     */
    public function crearTitularidadInicial(string $idTarjeta, int $docUsuario, ?string $fechaInicio = null): TitularidadTarjeta
    {
        $fecha = $fechaInicio ?? Carbon::now()->toDateString();

        $existeActiva = TitularidadTarjeta::query()
            ->where('id_tarjeta', $idTarjeta)
            ->where('id_estado', self::ESTADO_ACTIVO)
            ->whereNull('fecha_fin')
            ->exists();

        if ($existeActiva) {
            throw new InvalidArgumentException('La tarjeta ya tiene una titularidad activa.');
        }

        return $this->cambiarTitular($idTarjeta, $docUsuario, 'Asignacion inicial', $fecha);
    }
}
