<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleMantenimiento extends Model
{
    protected $table = 'detalle_mantenimiento';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_mantenimiento',
        'id_tipo_mantenimiento',
        'descripcion',
        'evidencia_foto',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'id_mantenimiento', 'id_mantenimiento');
    }

    public function tipoMantenimiento()
    {
        return $this->belongsTo(TipoMantenimiento::class, 'id_tipo_mantenimiento', 'id_tipo_mantenimiento');
    }
}
