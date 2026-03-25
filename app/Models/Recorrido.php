<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recorrido extends Model
{
    protected $table = 'recorridos';
    protected $primaryKey = 'id_recorrido';

    protected $fillable = [
        'placa',
        'id_ruta',
        'doc_us',
        'sentido',
        'hora_salida',
        'hora_llegada',
        'cantidad_pasajeros',
        'ingresos',
        'foto_torniquete'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    public function conductor()
    {
        return $this->belongsTo(Usuario::class, 'doc_us', 'doc_usuario');
    }

    public function novedades()
    {
        return $this->hasMany(NovedadRecorrido::class, 'id_recorrido', 'id_recorrido');
    }
}
