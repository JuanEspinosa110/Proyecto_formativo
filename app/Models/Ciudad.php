<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudad';
    protected $primaryKey = 'id_ciudad';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
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
}
