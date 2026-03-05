<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';
    protected $primaryKey = 'id_departamento';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nombre_departamento'
    ];

    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'id_departamento');
    }
}
