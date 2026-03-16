<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialBus extends Model
{
    protected $table = 'historial_buses';
    protected $primaryKey = 'id_historial';
    public $timestamps = false; // We use created_at only

    protected $fillable = [
        'placa',
        'id_ruta',
        'doc_us',
        'tipo_cambio',
        'detalle'
    ];

    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    public function conductor()
    {
        return $this->belongsTo(Usuario::class, 'doc_us', 'doc_usuario');
    }
}
