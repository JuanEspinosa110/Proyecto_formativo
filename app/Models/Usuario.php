<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use App\Models\TipoUsuario;
use App\Models\Empresa;
use App\Models\Ciudad;
use App\Models\TitularidadTarjeta;

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
        'fecha_nacimiento',
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
                    'admin' => 1,
                    'operador' => 4,
                    'usuario' => 3,
                    'controlador_tiempo' => 8,
                    default => 1
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
     * Obtener el estado del usuario
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
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

    public function titularidadesTarjeta(): HasMany
    {
        return $this->hasMany(TitularidadTarjeta::class, 'doc_usuario', 'doc_usuario');
    }

    public function getActiveNit()
    {
        // Retorna el valor de la columna NIT de la tabla usuario
        return $this->NIT;
    }

    /**
     * Determina si el usuario tiene un rol específico o acceso por jerarquía.
     */
    public function hasRole($role): bool
    {
        $roleMap = [
            'admin' => 1,
            'pasajero' => 2,
            'conductor' => 3,
            'auxiliar' => 4,
            'propietario' => 5,
            'gestor_setp' => 6,
            'coordinador_bus' => 7,
            'gestor_recargas' => 8,
            'jefe_mantenimiento' => 9,
        ];

        $targetId = is_numeric($role) ? (int) $role : ($roleMap[$role] ?? null);

        if (!$targetId)
            return false;

        // El usuario tiene el rol exacto
        if ((int) $this->id_tipo_usuario === $targetId) {
            return true;
        }

        // Lógica de herencia: Cualquier usuario autenticado tiene acceso a "pasajero" (ID 2)
        if ($targetId === 2) {
            return true;
        }

        return false;
    }

    /**
     * Obtiene el nombre de la ruta del dashboard principal según el rol.
     */
    public function getDashboardRoute(): string
    {
        return match ((int) $this->id_tipo_usuario) {
            1 => 'admin.dashboard',
            3 => 'conductor.dashboard',
            4 => 'empresa.dashboard',
            5 => 'propietario.dashboard',
            6 => 'gestor_setp.dashboard',
            7 => 'coordinador_bus.dashboard',
            8 => 'gestor_recargas.dashboard', // O gestor_recargas
            9 => 'jefemantenimiento.dashboard',
            default => 'pasajero.dashboard',
        };
    }
}
