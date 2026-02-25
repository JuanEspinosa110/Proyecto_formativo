<?php

namespace App\Http\Controllers\SuperAdmin\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Configuracion\TipoDocumentoRequest;
use App\Services\SuperAdmin\Configuracion\TipoDocumentoService;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    protected $service;

    public function __construct(TipoDocumentoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $tipos = $this->service->paginate(5, $request->buscar);
        return view('superadmin.configuracion.tipo_documento.index', compact('tipos'));
    }

    public function store(TipoDocumentoRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->back()->with('success', 'Tipo de documento creado correctamente.');
    }

    public function update(TipoDocumentoRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->back()->with('success', 'Tipo de documento actualizado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        return $this->service->exportExcel($request->buscar);
    }
}
