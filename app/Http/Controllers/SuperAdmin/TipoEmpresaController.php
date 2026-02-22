<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoEmpresa;

class TipoEmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoEmpresa::orderBy('nombre_tipo');

        if ($request->filled('buscar')) {
            $query->where('nombre_tipo', 'LIKE', '%' . $request->buscar . '%');
        }

        $tipos = $query->paginate(5)->withQueryString();

        return view('superadmin.configuracion.tipo_empresa.index', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_tipo' => 'required|string|max:100|unique:tipo_empresa,nombre_tipo'
        ], [
            'nombre_tipo.required' => 'El nombre del tipo de empresa es obligatorio.',
            'nombre_tipo.unique' => 'Este tipo de empresa ya existe.',
            'nombre_tipo.max' => 'Máximo 100 caracteres permitidos.'
        ]);

        TipoEmpresa::create([
            'nombre_tipo' => strtoupper($request->nombre_tipo)
        ]);

        return redirect()->route('superadmin.tipo-empresa.index')
            ->with('success', 'Tipo de empresa creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoEmpresa::findOrFail($id);

        $request->validate([
            'nombre_tipo' => 'required|string|max:100|unique:tipo_empresa,nombre_tipo,' . $id . ',id_tipo_empresa'
        ], [
            'nombre_tipo.required' => 'El nombre es obligatorio.',
            'nombre_tipo.unique' => 'Este tipo ya existe.',
        ]);

        $tipo->update([
            'nombre_tipo' => strtoupper($request->nombre_tipo)
        ]);

        return redirect()->route('superadmin.tipo-empresa.index')
            ->with('success', 'Tipo de empresa actualizado correctamente.');
    }
}