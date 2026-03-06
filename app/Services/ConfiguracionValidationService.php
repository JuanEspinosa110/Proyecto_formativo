<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ConfiguracionValidationService
{
    /**
     * Valida la existencia de un registro con nombre similar (case-insensitive y trim).
     *
     * @param string $modelClass Clase del Modelo (ej: App\Models\Estado::class)
     * @param string $column Nombre de la columna de texto (ej: 'nombre_estado')
     * @param string $value Valor a validar
     * @param string $fieldForError Nombre del campo para el mensaje de error (ej: 'nombre_estado')
     * @param mixed $excludeId ID a excluir en caso de actualización
     * @param string $pkName Nombre de la llave primaria
     * @throws ValidationException
     */
    public static function validarNombreUnico($modelClass, $column, $value, $fieldForError, $excludeId = null, $pkName = 'id')
    {
        $normalizedValue = strtolower(trim($value));

        $query = $modelClass::whereRaw("LOWER($column) = ?", [$normalizedValue]);

        if ($excludeId !== null) {
            $query->where($pkName, '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                $fieldForError => ['Ya existe un registro con un nombre similar.']
            ]);
        }
    }
}
