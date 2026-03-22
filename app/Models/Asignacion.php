<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    /**
     * El nombre de la tabla asociada al modelo.
     * Siguiendo el esquema actual donde 'viaje' representa la asignación de bus, ruta y conductor.
     */
    protected $table = 'asignacion';
    protected $primaryKey = 'id_asignacion';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_asignacion',
        'placa',
        'doc_usuario',
        'id_ruta',
        'fecha_inicio',
        'fecha_fin',
        'id_estado',
        'Nit'
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
        return $this->belongsTo(Usuario::class, 'doc_usuario', 'doc_usuario');
    }

    /**
     * Alias para conductor para compatibilidad con las vistas
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'doc_usuario', 'doc_usuario');
    }

    /**
     * Relación con el Estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con la Empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'Nit', 'NIT');
    }

    /**
     * Relación con los recorridos realizados durante este turno (vinculados por placa y conductor)
     */
    public function viajes()
    {
        return $this->hasMany(Viaje::class, 'placa', 'placa');
    }

    public function recorridos()
    {
        return $this->hasManyThrough(Recorrido::class, Viaje::class, 'placa', 'id_viaje', 'placa', 'id_viaje');
    }
}
