<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorCode extends Model
{
    use HasFactory;

    protected $table = 'two_factor_codes';

    protected $fillable = [
        'documento',
        'tipo_usuario',
        'codigo',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Verifica si el código ha expirado
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
