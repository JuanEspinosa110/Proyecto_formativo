<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaViaje extends Model
{
    protected $table = 'venta_viaje';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;

    protected $fillable = [
        'id_viaje',
        'id_tarjeta',
        'valor',
        'fecha',
        'id_estado'
    ];

    /**
     * Relación con el Viaje.
     */
    public function viaje()
    {
        return $this->belongsTo(Viaje::class, 'id_viaje', 'id_viaje');
    }

    /**
     * Relación con la Tarjeta.
     */
    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class, 'id_tarjeta', 'id_tarjeta');
    }
}

