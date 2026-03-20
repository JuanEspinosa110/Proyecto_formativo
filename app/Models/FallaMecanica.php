<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FallaMecanica extends Model
{
    use HasFactory;

    protected $table = 'reportes_fallas';
    protected $primaryKey = 'id_reporte';
    public $timestamps = true;

    protected $fillable = [
        'placa',
        'doc_usuario',
        'descripcion',
        'nivel_urgencia',
        'id_estado'
    ];

    /**
     * Relación con el Bus.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }
}
