<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'bus';
    protected $primaryKey = 'placa';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'placa',
        'NIT',
        'modelo',
        'capacidad_pasajeros',
        'kilometraje',
        'id_estado',
        'linc_transito',
        'numero_chasis',
        'numero_motor',
        'doc_propietario',
        'nombre_propietario',
        'telefono',
        'correo'
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'NIT', 'NIT');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'placa', 'placa');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'placa', 'placa');
    }

    public function recorridos()
    {
        return $this->hasManyThrough(Recorrido::class, Viaje::class, 'placa', 'id_viaje', 'placa', 'id_viaje');
    }

    /**
     * Verifica si el bus tiene todos los documentos requeridos aprobados y vigentes.
     */
    public function isOperable()
    {
        // 1. Obtener tipos de documento requeridos para Bus (requiere_placa = 1)
        $requiredTypes = TipoDocumento::where('requiere_placa', 1)
            ->where('id_estado', 1) // Activo
            ->pluck('id_tipo_documento');

        if ($requiredTypes->isEmpty()) {
            return true; // Si no hay requeridos, es operable por defecto
        }

        // 2. Contar documentos aprobados y vigentes para este bus (por tipo de documento)
        $approvedCount = Documento::where('placa', $this->placa)
            ->whereIn('id_tipo_documento', $requiredTypes)
            ->where('id_estado', 1) // 1 = ACTIVO/APROBADO
            ->where('fecha_vencimiento', '>', now())
            ->distinct('id_tipo_documento')
            ->count('id_tipo_documento');

        // 3. Debe tener exactamente un documento aprobado por cada tipo requerido
        return $approvedCount === $requiredTypes->count();
    }
}
