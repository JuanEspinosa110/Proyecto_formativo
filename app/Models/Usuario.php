<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\TipoUsuario;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'doc_usuario';
    public $incrementing = false;
    protected $keyType = 'integer';
    public $timestamps = false;


    protected $fillable = [
        'doc_usuario',
        'NIT',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'correo',
        'password',
        'telefono',
        'foto_usuario',
        'id_tipo_usuario',
        'id_ciudad',
        'id_estado',
        // Atributos de compatibilidad con validaciones/controladores antiguos
        'nombre',
        'apellido',
        'rol',
        'estado'
    ];

    protected $hidden = ['password'];

    protected static function booted()
    {
        static::creating(function ($usuario) {
            // Mapeo de campos provenientes del controlador (nombre/apellido)
            if (isset($usuario->nombre) && empty($usuario->primer_nombre)) {
                $usuario->primer_nombre = $usuario->nombre;
            }
            if (isset($usuario->apellido) && empty($usuario->primer_apellido)) {
                $usuario->primer_apellido = $usuario->apellido;
            }

            // Mapeo de Rol (string de validación a ID real de base de datos)
            if (isset($usuario->rol)) {
                $usuario->id_tipo_usuario = match ($usuario->rol) {
                    'admin'    => 1, // ID para Admin
                    'operador' => 4, // ID para Operador (según tabla tipo_usuario)
                    'usuario'  => 3, // ID para Conductor/Usuario según lista permitida en vista
                    default    => 1
                };
            }

            // Mapeo de Estado (boolean de validación a ID real de estado)
            if (isset($usuario->estado)) {
                $usuario->id_estado = $usuario->estado ? 1 : 2; // 1: Activo, 2: Inactivo
            }

            // Asignación automática de NIT e id_ciudad si el Admin está en sesión
            if (auth()->check()) {
                $usuario->NIT = $usuario->NIT ?? auth()->user()->NIT;
                $usuario->id_ciudad = $usuario->id_ciudad ?? auth()->user()->id_ciudad;
            }

            // Garantizar estado activo si no está definido
            if (empty($usuario->id_estado)) {
                $usuario->id_estado = 1;
            }
        });
    }

    public function tipoUsuario()
    {
        return $this->belongsTo(TipoUsuario::class, 'id_tipo_usuario', 'id_tipo_usuario');
    }

    /**
     * Obtener la empresa asociada al usuario 
     */
    public function empresa()
    {
        // Relación: Un usuario pertenece a una empresa a través del NIT
        return $this->belongsTo(Empresa::class, 'NIT', 'NIT');
    }

    /**
     * Obtener la ciudad asociada al usuario
     */
    public function ciudad()
    {
        // Relación: Un usuario pertenece a una ciudad a través de id_ciudad
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id_ciudad');
    }
}
