<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SuperAdministrador extends Authenticatable
{
    use Notifiable;

    protected $table = 'super_administrador';
    protected $primaryKey = 'doc_super_admin';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'doc_super_admin',
        'nombre',
        'correo',
        'password',
        'id_estado',
        'fecha_creacion'
    ];

    protected $hidden = ['password'];

    public function getAuthIdentifierName()
    {
        return 'doc_super_admin';
    }
}

