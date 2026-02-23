<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use App\Models\Estado;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index(Request $request)
    {
        $tipos = TipoDocumento::with('estado')
            ->when($request->buscar, function ($query, $buscar) {
                return $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('nombre')
            ->paginate(5)
            ->withQueryString();

        $estados = Estado::all();

        return view('superadmin.configuracion.tipo_documento.index',
            compact('tipos', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100|unique:tipo_documento,nombre',
            'descripcion' => 'nullable|max:255',
            'id_estado' => 'required'
        ]);

        TipoDocumento::create($request->all());

        return redirect()
            ->route('superadmin.tipo_documento.index')
            ->with('success', 'Tipo de documento creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoDocumento::findOrFail($id);

        $request->validate([
            'nombre' => 'required|max:100|unique:tipo_documento,nombre,' . $id . ',id_tipo_documento',
            'descripcion' => 'nullable|max:255',
            'id_estado' => 'required'
        ]);

        $tipo->update($request->all());

        return redirect()
            ->route('superadmin.tipo_documento.index')
            ->with('success', 'Tipo de documento actualizado correctamente.');
    }
}