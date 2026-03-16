<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'viaje';

    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'id_viaje';

    /**
     * Indica si la clave primaria es autoincremental.
     */
    public $incrementing = false;

    /**
     * El tipo de dato de la clave primaria.
     */
    protected $keyType = 'int';

    /**
     * Indica si el modelo debe tener marcas de tiempo.
     */
    public $timestamps = false;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'id_viaje',
        'placa',
        'id_ruta',
        'doc_us',
        'fecha',
        'id_estado'
    ];

    /**
     * Relación con el Bus.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }

    /**
     * Relación con la Ruta.
     */
    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'id_ruta', 'id_ruta');
    }

    /**
     * Relación con el Usuario (Conductor).
     */
    public function conductor()
    {
        return $this->belongsTo(Usuario::class, 'doc_us', 'doc_usuario');
    }

    /**
     * Relación con el Estado.
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con las ventas del viaje.
     */
    public function ventas()
    {
        return $this->hasMany(VentaViaje::class, 'id_viaje', 'id_viaje');
    }
}
