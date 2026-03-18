<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCambiosLicencia extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios_licencia';

    protected $fillable = [
        'doc_conductor',
        'fecha_anterior',
        'fecha_vencimiento_anterior',
        'fecha_nueva',
        'fecha_vencimiento_nueva',
        'usuario_modifica'
    ];

    /**
     * Relación con el conductor
     */
    public function conductor()
    {
        return $this->belongsTo(Usuario::class, 'doc_conductor', 'doc_usuario');
    }

    /**
     * Relación con el usuario que modifica
     */
    public function usuarioModifica()
    {
        return $this->belongsTo(Usuario::class, 'usuario_modifica', 'doc_usuario');
    }
}
