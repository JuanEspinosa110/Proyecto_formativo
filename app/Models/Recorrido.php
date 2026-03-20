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
        'hora_salida',
        'hora_llegada',
        'cantidad_pasajeros',
        'ingresos'
    ];

    protected static function booted()
    {
        static::saving(function ($recorrido) {
            if ($recorrido->cantidad_pasajeros) {
                $recorrido->ingresos = $recorrido->cantidad_pasajeros * 3300;
            }
        });
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }

    public function novedades()
    {
        return $this->hasMany(NovedadRecorrido::class, 'id_recorrido', 'id_recorrido');
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    public function conductor()
    {
        return $this->belongsTo(Usuario::class, 'doc_us', 'doc_usuario');
    }
}
