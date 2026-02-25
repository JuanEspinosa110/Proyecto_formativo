<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAsignacion extends Model
{
    protected $table = 'tipo_asignacion';
    protected $primaryKey = 'id_tipo_asignacion';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tipo'
    ];
}
