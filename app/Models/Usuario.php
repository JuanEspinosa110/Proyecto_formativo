<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'doc_us';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'doc_us',
        'NIT',
        'primer_nombre',
        'primer_apellido',
        'segundo_apellido',
        'correo',
        'password',
        'telefono',
        'foto_usuario',
        'id_tipo_usuario',
        'id_ciudad',
        'id_estado'
    ];

    protected $hidden = ['password'];
}

