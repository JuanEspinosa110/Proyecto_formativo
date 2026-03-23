<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoUsuario extends Model
{
    protected $table = 'tipo_usuario';
    protected $primaryKey = 'id_tipo_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre_tipo',
        'id_estado'
    ];

    /**
     * Relación con el Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }
}
