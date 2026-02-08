<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';

    protected $primaryKey = 'NIT'; 

    public $timestamps = true;

    protected $fillable = [
        'NIT',
        'nombre_empresa',
        'doc_representante',
        'primer_nombre_repre',
        'segundo_nombre_repre',
        'primer_apellido_repre',
        'segundo_apellido_repre',
        'telefono_representante',
        'correo_representante',
        'telefono_empresa',
        'correo_corporativo',
        'id_ciudad',
        'id_estado',
        'fecha_creacion'
    ];
}

