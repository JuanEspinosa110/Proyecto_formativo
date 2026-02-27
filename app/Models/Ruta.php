<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = 'ruta';
    protected $primaryKey = 'id_ruta';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'NIT',
        'id_ciudad',
        'id_barrio_origen',
        'origen',
        'id_barrio_destino',
        'destino',
        'id_estado'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'NIT', 'NIT');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id_ciudad');
    }

    public function barrioOrigen()
    {
        return $this->belongsTo(Barrio::class, 'id_barrio_origen', 'id_barrio');
    }

    public function barrioDestino()
    {
        return $this->belongsTo(Barrio::class, 'id_barrio_destino', 'id_barrio');
    }

    // El nombre de la ruta se puede deducir de origen - destino
    public function getNombreRutaAttribute()
    {
        return ($this->origen ?? 'N/A') . ' - ' . ($this->destino ?? 'N/A');
    }
}
