<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barrio extends Model
{
    /**
     * @var string
     */
    protected $table = 'barrio';

    /**
     * @var string
     */
    protected $primaryKey = 'id_barrio';

    /**
     * @var bool
     */
    public $incrementing = true;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'nombre',
        'id_ciudad',
        'latitud',
        'longitud'
    ];

    /**
     * Relación con la ciudad a la que pertenece el barrio
     */
    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id_ciudad');
    }

    /**
     * Relación con Rutas (como origen)
     */
    public function rutasOrigen(): HasMany
    {
        return $this->hasMany(Ruta::class, 'id_barrio_origen', 'id_barrio');
    }

    /**
     * Relación con Rutas (como destino)
     */
    public function rutasDestino(): HasMany
    {
        return $this->hasMany(Ruta::class, 'id_barrio_destino', 'id_barrio');
    }
}
