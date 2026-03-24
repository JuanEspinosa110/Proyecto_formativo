<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $table = 'mantenimiento';
    protected $primaryKey = 'id_mantenimiento';
    public $timestamps = false;

    protected $fillable = [
        'placa',
        'NIT',
        'kilometraje',
        'fecha_mantenimiento',
        'fecha_proximo',
        'km_proximo',
        'costo_total',
        'id_estado',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'NIT', 'NIT');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleMantenimiento::class, 'id_mantenimiento', 'id_mantenimiento');
    }
}
