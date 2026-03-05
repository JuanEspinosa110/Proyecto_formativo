<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEmpresa extends Model
{
    protected $table = 'tipo_empresa';
    protected $primaryKey = 'id_tipo_empresa';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tipo'
    ];

    public function empresa()
    {
        return $this->hasMany(Empresa::class, 'id_tipo_empresa', 'id_tipo_empresa');
    }
}