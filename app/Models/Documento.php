<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';
    public $timestamps = true;

    protected $fillable = [
        'id_documento',
        'nombre',
        'archivo',
        'fecha_expedicion',
        'fecha_vencimiento',
        'id_tipo_documento',
        'doc_usuario',
        'NIT',
        'placa',
        'id_estado'
    ];

    protected $casts = [
        'fecha_expedicion' => 'date',
        'fecha_vencimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación: Un documento pertenece a un tipo de documento
     */
    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento', 'id_tipo_documento');
    }

    /**
     * Relación: Un documento pertenece a un estado
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado', 'id_estado');
    }

    /**
     * Relación: Un documento pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'NIT', 'NIT');
    }

    /**
     * Relación: Un documento puede estar asociado a un usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'doc_usuario', 'doc_usuario');
    }

    /**
     * Relación: Un documento puede estar asociado a un bus
     */
    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class, 'placa', 'placa');
    }

    // Métodos auxiliares
    public function getEstadoExpiracionAttribute()
    {
        if ($this->id_estado == 2 || $this->id_estado == 22) {
            return 'ARCHIVADO';
        }

        $hoy = now()->startOfDay();
        $vencimiento = $this->fecha_vencimiento->startOfDay();

        if ($vencimiento->lt($hoy)) {
            return 'VENCIDO';
        }

        if ($hoy->diffInDays($vencimiento) <= 15) {
            return 'PRÓXIMO A VENCER';
        }

        return 'VIGENTE';
    }

    public function getStatusColorAttribute()
    {
        return match($this->estado_expiracion) {
            'VENCIDO' => 'danger',
            'PRÓXIMO A VENCER' => 'warning',
            'ARCHIVADO' => 'secondary',
            default => 'success',
        };
    }

    public function isVigente()
    {
        return $this->estado_expiracion === 'VIGENTE';
    }

    public function isVencido()
    {
        return $this->estado_expiracion === 'VENCIDO';
    }

    public function isProximoAVencer()
    {
        return $this->estado_expiracion === 'PRÓXIMO A VENCER';
    }

    public function diasParaVencimiento()
    {
        return now()->diffInDays($this->fecha_vencimiento, false);
    }
}