<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteFalla extends Model
{
    protected $table = 'reportes_fallas';
    protected $primaryKey = 'id_reporte';

    protected $fillable = [
        'placa',
        'doc_usuario',
        'descripcion',
        'nivel_urgencia',
        'id_estado',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }

    public function conductor()
    {
        return $this->belongsTo(Usuario::class, 'doc_usuario', 'doc_usuario');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'id_reporte', 'id_reporte');
    }
}
