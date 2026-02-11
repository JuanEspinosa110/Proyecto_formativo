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
}

