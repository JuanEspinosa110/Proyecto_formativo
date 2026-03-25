<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NovedadRecorrido extends Model
{
    protected $table = 'novedad_recorridos';
    protected $primaryKey = 'id_novedad';

    protected $fillable = [
        'id_recorrido',
        'doc_controlador',
        'tipo',
        'descripcion'
    ];

    public function recorrido()
    {
        return $this->belongsTo(Recorrido::class, 'id_recorrido', 'id_recorrido');
    }

    public function controlador()
    {
        return $this->belongsTo(Usuario::class, 'doc_controlador', 'doc_usuario');
    }
}
