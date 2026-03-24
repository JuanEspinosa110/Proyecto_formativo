<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarjeta extends Model
{
    protected $table = 'tarjeta';

    protected $primaryKey = 'id_tarjeta';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false; // si tu tabla no tiene timestamps

    protected $fillable = [
        'id_tarjeta',
        'codigo_tarjeta',
        'saldo',
        'id_estado',
        'doc_usuario',
    ];

    public function usuarioActual(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'doc_usuario', 'doc_usuario');
    }

    public function titularidades(): HasMany
    {
        return $this->hasMany(TitularidadTarjeta::class, 'id_tarjeta', 'id_tarjeta');
    }
}

