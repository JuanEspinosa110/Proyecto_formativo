<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $table = 'usuario';
    protected $primaryKey = 'doc_usuario';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'doc_usuario',
        'NIT',
        'primer_nombre',
        'primer_apellido',
        'segundo_apellido',
        'correo',
        'password',
        'telefono',
        'foto_usuario',
        'id_tipo_usuario',
        'id_ciudad',
        'id_estado'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con la Empresa (por el NIT del usuario)
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'NIT', 'NIT');
    }

    /**
     * Obtener el NIT real de operación para este usuario.
     * Prioriza el NIT de la empresa donde el usuario es representante legal.
     * Si no es representante, usa el NIT almacenado en su perfil de usuario (Empleado).
     */
    public function getActiveNit()
    {
        // 1. Verificar si el usuario es representante legal de alguna empresa
        $empresaRepresentada = Empresa::where('doc_representante', $this->doc_usuario)->first();
        
        if ($empresaRepresentada) {
            return $empresaRepresentada->NIT;
        }

        // 2. Fallback al NIT de la tabla usuario (para empleados o si no se encontró representación)
        return $this->NIT;
    }
}
