<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';
    protected $primaryKey = 'id_departamento';
    public $timestamps = false;

    protected $fillable = [
        'nombre_departamento',
    ];

    /**
     * Relación con Ciudades
     */
    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'id_departamento', 'id_departamento');
    }
}
