<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    protected $table = 'tipo_usuario'; // nombre exacto de la tabla
    protected $primaryKey = 'id_tipo_usuario'; // PK personalizada
    public $timestamps = false; // si tu tabla NO tiene created_at y updated_at

    protected $fillable = [
        'nombre_tipo'
    ];
}