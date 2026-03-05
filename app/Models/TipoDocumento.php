<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documento';
    protected $primaryKey = 'id_tipo_documento';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'requiere_doc_usuario',
        'requiere_placa',
        'id_estado'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }
}