<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recarga extends Model
{
    use HasFactory;

    protected $table = 'recarga';
    protected $primaryKey = 'id_recarga';

    // Como es un BIGINT UNSIGNED, Laravel lo maneja bien,
    // pero si no fuera autoincremental, deberías ponerlo en false.
    public $incrementing = true;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_recarga',
        'id_tarjeta',
        'monto',
        'doc_usuario_gestor',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'monto' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con Tarjeta
    public function tarjeta()
    {
        return $this->belongsTo(Tarjeta::class, 'id_tarjeta', 'id_tarjeta');
    }

    // Relación con el Gestor que hizo la recarga
    public function gestor()
    {
        return $this->belongsTo(Usuario::class, 'doc_usuario_gestor', 'doc_usuario');
    }

}
