<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcesionRuta extends Model
{
    protected $table = 'concesion_ruta';
    protected $primaryKey = 'id_concesion';
    public $timestamps = false;

    protected $fillable = [
        'NIT',
        'id_ruta',
        'fecha_inicio',
        'fecha_fin',
        'id_estado'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'NIT', 'NIT');
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }
}
