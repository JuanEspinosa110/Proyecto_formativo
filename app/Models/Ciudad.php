<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudad';
    protected $primaryKey = 'id_ciudad';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_ciudad',
        'nombre_city',
        'id_departamento',
    ];

    /**
     * Relación con Departamento
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }

    /**
     * Relación con Empresas
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'id_ciudad', 'id_ciudad');
    }

    /**
     * Relación con Barrios
     */
    public function barrios()
    {
        return $this->hasMany(Barrio::class, 'id_ciudad', 'id_ciudad');
    }

    /**
     * Relación con Usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_ciudad', 'id_ciudad');
    }
}
