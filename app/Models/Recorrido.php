<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recorrido extends Model
{
    protected $table = 'recorridos';
    protected $primaryKey = 'id_recorrido';

    protected $fillable = [
        'id_viaje',
        'sentido',
        'hora_salida',
        'hora_llegada',
        'foto_torniquete'
    ];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class, 'id_viaje', 'id_viaje');
    }

    public function novedades()
    {
        return $this->hasMany(NovedadRecorrido::class, 'id_recorrido', 'id_recorrido');
    }
}
