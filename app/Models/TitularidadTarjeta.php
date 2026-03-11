<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TitularidadTarjeta extends Model
{
    protected $table = 'titularidad_tarjeta';

    protected $primaryKey = 'id_titularidad_tarjeta';

    public $timestamps = false;

    protected $fillable = [
        'id_tarjeta',
        'doc_usuario',
        'fecha_inicio',
        'fecha_fin',
        'id_estado',
        'motivo_cambio',
    ];

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(Tarjeta::class, 'id_tarjeta', 'id_tarjeta');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'doc_usuario', 'doc_usuario');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }
}
