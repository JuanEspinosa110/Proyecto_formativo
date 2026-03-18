<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Str;

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
        'doc_usuario',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tarjeta) {
            // Generar id_tarjeta aleatorio de 16 caracteres si no está definido
            if (empty($tarjeta->id_tarjeta)) {
                $tarjeta->id_tarjeta = Str::random(16);
            }

            // Generar código secuencial para codigo_tarjeta si no está definido
            if (empty($tarjeta->codigo_tarjeta)) {
                $ultimo = self::max('codigo_tarjeta');
                $nuevoCodigo = $ultimo ? (int)$ultimo + 1 : 1000001;
                $tarjeta->codigo_tarjeta = (string) $nuevoCodigo;
            }

            // Estado por defecto: buscar el id del estado 'ACTIVO'
            if (empty($tarjeta->id_estado)) {
                $estadoActivo = \DB::table('estado')->where('nombre_estado', 'ACTIVO')->value('id_estado');
                $tarjeta->id_estado = $estadoActivo;
            }
        });
    }

    public function usuarioActual(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'doc_usuario', 'doc_usuario');
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
        $nuevoEstado = \DB::table('estado')->where('nombre', $nombreEstado)->value('id_estado');
        if ($nuevoEstado) {
            $this->id_estado = $nuevoEstado;
            $this->save();
        }
    }
}

