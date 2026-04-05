<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Tarjeta extends Model
{
    protected $table = 'tarjeta';

    protected $primaryKey = 'id_tarjeta';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_tarjeta',
        'codigo_tarjeta',
        'saldo',
        'id_estado',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tarjeta) {
            // Generar id_tarjeta aleatorio de 12 caracteres si no está definido
            if (empty($tarjeta->id_tarjeta)) {
                $tarjeta->id_tarjeta = Str::upper(Str::random(12));
            }

            // Generar código secuencial para codigo_tarjeta si no está definido
            if (empty($tarjeta->codigo_tarjeta)) {
                $ultimo = self::max('codigo_tarjeta');
                $nuevoCodigo = $ultimo ? (int)$ultimo + 1 : 1000001;
                $tarjeta->codigo_tarjeta = (string) $nuevoCodigo;
            }

            // Estado por defecto: buscar el id del estado 'ACTIVO'
            if (empty($tarjeta->id_estado)) {
                $estadoActivo = DB::table('estado')->where('nombre_estado', 'ACTIVO')->value('id_estado');
                $tarjeta->id_estado = $estadoActivo;
            }
        });
    }

    /**
     * Obtiene el usuario actual vinculado a esta tarjeta a través de la titularidad activa.
     */
    public function usuarioActual()
    {
        return $this->hasOneThrough(
            Usuario::class,
            TitularidadTarjeta::class,
            'id_tarjeta', // FK en TitularidadTarjeta
            'doc_usuario', // FK en Usuario
            'id_tarjeta', // Local Key en Tarjeta
            'doc_usuario'  // Local Key en TitularidadTarjeta
        )->where('titularidad_tarjeta.id_estado', 1);
    }

    public function titularidades(): HasMany
    {
        return $this->hasMany(TitularidadTarjeta::class, 'id_tarjeta', 'id_tarjeta');
    }

    public function estado()
    {
        return $this->belongsTo(\App\Models\Estado::class, 'id_estado', 'id_estado');
    }

    // Métodos para cambiar estado usando el nombre del estado
    public function cambiarEstadoPorNombre($nombreEstado)
    {
        $nuevoEstado = DB::table('estado')->where('nombre', $nombreEstado)->value('id_estado');
        if ($nuevoEstado) {
            $this->id_estado = $nuevoEstado;
            $this->save();
        }
    }
}

