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
<<<<<<< HEAD
        'id_ruta',
        'id_ciudad',
        'id_barrio_origen',
        'id_barrio_destino',
        'id_estado'
=======
        'NIT', 'id_ciudad', 'id_barrio_origen', 
        'origen', 'id_barrio_destino', 'destino', 'id_estado'
>>>>>>> 46a0f22cc73e44ddec95c253bea0afad04e6f84e
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
