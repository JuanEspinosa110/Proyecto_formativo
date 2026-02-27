<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'bus';
    protected $primaryKey = 'placa';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'placa',
        'NIT',
        'modelo',
        'capacidad_pasajeros',
        'kilometraje',
        'id_estado',
        'linc_transito',
        'numero_chasis',
        'numero_motro',
        'doc_propietario',
        'nombre_propietario',
        'telefono',
        'correo'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'NIT', 'NIT');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }
}
