<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = [
        'id_estado',
        'nombre_estado',
        'descripcion',
    ];

    /**
     * Relación con SuperAdministrador
     */
    public function superAdministradores()
    {
        return $this->hasMany(SuperAdministrador::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con Usuario
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con Empresa
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'id_estado', 'id_estado');
    }
}
