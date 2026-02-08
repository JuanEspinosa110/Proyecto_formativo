<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;

class SuperAdministrador extends Authenticatable
{
    protected $table = 'super_administrador';
    protected $primaryKey = 'doc_super_admin';
    public $incrementing = false;
    protected $keyType = 'bigint';
    public $timestamps = true;
    
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'doc_super_admin',
        'nombre',
        'correo',
        'telefono',
        'foto_perfil',
        'password',
        'id_estado',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación con actividad_log
     */
    public function actividadesLog()
    {
        return $this->hasMany(ActividadLog::class, 'doc_usuario', 'doc_super_admin')
            ->where('tipo_usuario', 0); // 0 = super admin
    }

    /**
     * Registrar actividad en el log
     */
    public function registrarActividad($accion, $modulo, $ipAddress = null)
    {
        return DB::table('actividad_log')->insert([
            'doc_usuario' => $this->doc_super_admin,
            'tipo_usuario' => 0, // 0 = super admin
            'accion' => $accion,
            'modulo' => $modulo,
            'ip_address' => $ipAddress ?? request()->ip(),
            'fecha_registro' => now(),
        ]);
    }

    /**
     * Obtener actividades recientes
     */
    public function actividadesRecientes($limit = 10)
    {
        return DB::table('actividad_log')
            ->where('doc_usuario', $this->doc_super_admin)
            ->where('tipo_usuario', 0)
            ->orderBy('fecha_registro', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword($newPassword)
    {
        $this->password = Hash::make($newPassword);
        $this->save();
        
        $this->registrarActividad('Cambio de contraseña', 'Perfil y Seguridad');
        
        return true;
    }

    /**
     * Verificar contraseña actual
     */
    public function verificarPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Actualizar foto de perfil
     */
    public function actualizarFoto($rutaFoto)
    {
        // Eliminar foto anterior si existe
        if ($this->foto_perfil && file_exists(public_path($this->foto_perfil))) {
            unlink(public_path($this->foto_perfil));
        }

        $this->foto_perfil = $rutaFoto;
        $this->save();

        $this->registrarActividad('Cambio de foto de perfil', 'Perfil y Seguridad');

        return true;
    }

    /**
     * Obtener URL de la foto de perfil
     */
    public function getFotoPerfilUrlAttribute()
    {
        if ($this->foto_perfil) {
            return asset($this->foto_perfil);
        }
        
        // Foto por defecto
        return asset('images/default-avatar.png');
    }

    /**
     * Obtener iniciales del nombre
     */
    public function getInicialesAttribute()
    {
        $nombres = explode(' ', $this->nombre);
        if (count($nombres) >= 2) {
            return strtoupper(substr($nombres[0], 0, 1) . substr($nombres[1], 0, 1));
        }
        return strtoupper(substr($this->nombre, 0, 2));
    }
}
