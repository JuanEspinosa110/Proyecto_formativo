<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadLog extends Model
{
    protected $table = 'actividad_log';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'doc_usuario',
        'tipo_usuario',
        'accion',
        'modulo',
        'ip_address',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Obtener el usuario asociado (puede ser super admin o usuario normal)
     */
    public function usuario()
    {
        if ($this->tipo_usuario == 0) {
            return $this->belongsTo(SuperAdministrador::class, 'doc_usuario', 'doc_super_admin');
        }
        return $this->belongsTo(Usuario::class, 'doc_usuario', 'doc_usuario');
    }

    /**
     * Scope para filtrar por tipo de usuario
     */
    public function scopeSuperAdmin($query)
    {
        return $query->where('tipo_usuario', 0);
    }

    /**
     * Scope para filtrar por usuario normal
     */
    public function scopeUsuarioNormal($query)
    {
        return $query->where('tipo_usuario', '>', 0);
    }

    /**
     * Scope para filtrar por módulo
     */
    public function scopeModulo($query, $modulo)
    {
        return $query->where('modulo', $modulo);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_registro', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener ícono según el módulo
     */
    public function getIconoAttribute()
    {
        $iconos = [
            'Perfil y Seguridad' => 'shield',
            'Usuarios' => 'group',
            'Empresas' => 'business',
            'Documentación' => 'description',
            'Tarjetas' => 'credit_card',
            'Licencias' => 'badge',
            'Roles y Permisos' => 'shield_person',
            'Reportes' => 'analytics',
            'Configuración' => 'settings',
            'Dashboard' => 'dashboard',
            'Sesión' => 'login',
        ];

        return $iconos[$this->modulo] ?? 'event_note';
    }

    /**
     * Obtener color según el tipo de acción
     */
    public function getColorAttribute()
    {
        $accionLower = strtolower($this->accion);

        if (str_contains($accionLower, 'eliminar') || str_contains($accionLower, 'borrar')) {
            return 'danger';
        }
        if (str_contains($accionLower, 'crear') || str_contains($accionLower, 'registrar')) {
            return 'success';
        }
        if (str_contains($accionLower, 'editar') || str_contains($accionLower, 'actualizar') || str_contains($accionLower, 'cambio')) {
            return 'warning';
        }
        if (str_contains($accionLower, 'inicio de sesión') || str_contains($accionLower, 'login')) {
            return 'primary';
        }

        return 'secondary';
    }
}
