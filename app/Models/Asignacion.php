<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    /**
     * El nombre de la tabla asociada al modelo.
     * Siguiendo el esquema actual donde 'viaje' representa la asignación de bus, ruta y conductor.
     */
    protected $table = 'viaje';
    protected $primaryKey = 'id_viaje';
    public $timestamps = false;

    protected $fillable = [
        'placa',
        'id_ruta',
        'doc_us',
        'fecha',
        'id_estado'
    ];

    /**
     * Relación con el Bus
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }

    /**
     * Relación con la Ruta
     */
    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    /**
     * Relación con el Usuario (Conductor)
     */
    public function conductor()
    {
        return $this->belongsTo(Usuario::class, 'doc_us', 'doc_usuario');
    }

    /**
     * Alias para conductor para compatibilidad con las vistas
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'doc_us', 'doc_usuario');
    }

    /**
     * Relación con el Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }
}
