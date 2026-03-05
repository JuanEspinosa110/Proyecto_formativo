<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
public function index(Request $request)
    {
       $estados = Estado::when($request->buscar, function ($query, $buscar) {
            return $query->where('nombre_estado', 'like', "%{$buscar}%");
        })
        ->orderBy('id_estado', 'asc')
        ->paginate(5)
        ->withQueryString();

    return view('superadmin.configuracion.estados.index', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_estado' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $exists = Estado::whereRaw('LOWER(nombre_estado) = ?', [strtolower($value)])->exists();
                    if ($exists) {
                        $fail('El estado "' . $value . '" ya se encuentra registrado en el sistema.');
                    }
                }
            ]
        ], [
            'nombre_estado.required' => 'El nombre del estado es obligatorio.',
            'nombre_estado.string'   => 'El nombre debe ser una cadena de texto.',
            'nombre_estado.max'      => 'El nombre no puede superar los 100 caracteres.'
        ]);

        Estado::create([
            'nombre_estado' => strtoupper($request->nombre_estado)
        ]);

        return redirect()
            ->route('superadmin.estados.index')
            ->with('success', 'Estado creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);

        $request->validate([
            'nombre_estado' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = Estado::whereRaw('LOWER(nombre_estado) = ?', [strtolower($value)])
                        ->where('id_estado', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('El estado "' . $value . '" ya existe registrado en el sistema.');
                    }
                }
            ]
        ], [
            'nombre_estado.required' => 'El nombre del estado es obligatorio.',
            'nombre_estado.string'   => 'El nombre debe ser una cadena de texto.',
            'nombre_estado.max'      => 'El nombre no puede superar los 100 caracteres.'
        ]);

        $estado->update([
            'nombre_estado' => strtoupper($request->nombre_estado)
        ]);

        return redirect()
            ->route('superadmin.estados.index')
            ->with('success', 'Estado actualizado correctamente.');
    }
}