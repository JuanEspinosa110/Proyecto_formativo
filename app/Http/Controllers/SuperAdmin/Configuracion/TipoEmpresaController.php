<?php

namespace App\Http\Controllers\SuperAdmin\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Configuracion\TipoEmpresaRequest;
use App\Services\SuperAdmin\Configuracion\TipoEmpresaService;
use Illuminate\Http\Request;

class TipoEmpresaController extends Controller
{
    protected $service;

    public function __construct(TipoEmpresaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $tipos = $this->service->paginate(5, $request->buscar);
        return view('superadmin.configuracion.tipo_empresa.index', compact('tipos'));
    }

    public function store(TipoEmpresaRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->back()->with('success', 'Tipo de empresa creado correctamente.');
    }

    public function update(TipoEmpresaRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->back()->with('success', 'Tipo de empresa actualizado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        return $this->service->exportExcel($request->buscar);
    }
}
