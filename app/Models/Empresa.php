<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'NIT';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'NIT',
        'nombre_empresa',
        'telefono_empresa',
        'correo_corporativo',
        'doc_representante',
        'primer_nombre_repre',
        'segundo_nombre_repre',
        'primer_apellido_repre',
        'segundo_apellido_repre',
        'telefono_representante',
        'correo_representante',
        'id_ciudad',
        'id_estado',
        'id_tipo_empresa'
    ];

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id_ciudad');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'NIT', 'NIT');
    }

    public function getNombreCompletoRepresentanteAttribute()
    {
        return trim(
            $this->primer_nombre_repre . ' ' .
            $this->segundo_nombre_repre . ' ' .
            $this->primer_apellido_repre . ' ' .
            $this->segundo_apellido_repre
        );
    }
}
