<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $primaryKey = 'id_documento';

    public $timestamps = true;

    protected $fillable = [
        'id_documento',
        'nombre',
        'archivo',
        'fecha_expedicion',
        'fecha_vencimiento',
        'id_tipo_documento',
        'doc_usuario',
        'NIT',
        'placa',
        'id_estado'
    ];

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }
}

