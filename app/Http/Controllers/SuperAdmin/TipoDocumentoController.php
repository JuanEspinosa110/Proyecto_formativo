<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use App\Models\Estado;
use App\Http\Requests\SuperAdmin\Configuracion\TipoDocumentoRequest;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index(Request $request)
    {
        $tipos = TipoDocumento::with('estado')
            ->when($request->buscar, function ($query, $buscar) {
                return $query->where('nombre', 'like', "%{$buscar}%");
            })
            ->orderBy('id_tipo_documento', 'asc')
            ->paginate(5)
            ->withQueryString();

        $estados = Estado::whereIn('id_estado', [1, 2])->get();

        return view('superadmin.configuracion.tipo_documento.index',
            compact('tipos', 'estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'               => 'required|string|max:100|unique:tipo_documento,nombre',
            'descripcion'          => 'nullable|string|max:255',
            'id_estado'            => 'required|in:1,2'
        ], [
            'nombre.required'      => 'El nombre del tipo de documento es obligatorio.',
            'nombre.unique'        => 'Ya existe un tipo de documento con este nombre.',
            'id_estado.required'   => 'El estado es obligatorio.',
            'id_estado.in'         => 'El estado seleccionado no es válido (solo Activo/Inactivo).'
        ]);

        TipoDocumento::create([
            'nombre'               => $validated['nombre'],
            'descripcion'          => $validated['descripcion'],
            'requiere_doc_usuario' => $request->has('requiere_doc_usuario'),
            'requiere_placa'       => $request->has('requiere_placa'),
            'id_estado'            => $validated['id_estado'],
        ]);

        return redirect()
            ->route('superadmin.tipo_documento.index')
            ->with('success', 'Tipo de documento creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoDocumento::findOrFail($id);

        // Usar el FormRequest para validación
        $validated = app(TipoDocumentoRequest::class)->validated();

        $tipo->nombre = $validated['nombre'];
        $tipo->descripcion = $validated['descripcion'] ?? null;
        $tipo->id_estado = $validated['id_estado'];
        $tipo->requiere_doc_usuario = $request->input('requiere_doc_usuario', 0) == 1 ? 1 : 0;
        $tipo->requiere_placa = $request->input('requiere_placa', 0) == 1 ? 1 : 0;
        $tipo->save();

        return redirect()
            ->route('superadmin.configuracion.tipo-documento.index')
            ->with('success', 'Tipo de documento actualizado correctamente.');
    }
}
