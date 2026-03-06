<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
<<<<<<< HEAD
    protected $table = 'tipo_usuario'; 
    protected $primaryKey = 'id_tipo_usuario'; 
    public $timestamps = false; 
=======
    protected $table = 'tipo_usuario';
    protected $primaryKey = 'id_tipo_usuario';
    public $timestamps = false;
>>>>>>> f31dff1adf54449ad08bd0def50691dd807aff5f

    protected $fillable = [
        'nombre_tipo'
    ];
}
