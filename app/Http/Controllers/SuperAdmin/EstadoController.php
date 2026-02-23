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
            'nombre_estado' => 'required|string|max:100|unique:estado,nombre_estado'
        ]);

        Estado::create([
            'nombre_estado' => $request->nombre_estado
        ]);

        return redirect()
            ->route('superadmin.estados.index')
            ->with('success', 'Estado creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);

        $request->validate([
            'nombre_estado' => 'required|string|max:100|unique:estado,nombre_estado,' . $id . ',id_estado'
        ]);

        $estado->update([
            'nombre_estado' => $request->nombre_estado
        ]);

        return redirect()
            ->route('superadmin.estados.index')
            ->with('success', 'Estado actualizado correctamente.');
    }
}