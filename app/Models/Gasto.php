<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';
    protected $primaryKey = 'id_gasto';

    protected $fillable = [
        'placa',
        'fecha',
        'tipo_gasto',
        'descripcion',
        'valor'
    ];

    /**
     * Relación con el Bus
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }
}
