<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    protected $table = 'ruta';
    protected $primaryKey = 'id_ruta';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_ruta',
        'id_ciudad',
        'codigo_ruta',
        'id_barrio_origen',
        'id_barrio_destino',
        'id_estado',
    ];

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

    // El nombre de la ruta se deduce de los barrios
    public function getNombreRutaAttribute()
    {
        $origen = $this->barrioOrigen->nombre ?? 'N/A';
        $destino = $this->barrioDestino->nombre ?? 'N/A';
        return $origen . ' - ' . $destino;
    }
}
