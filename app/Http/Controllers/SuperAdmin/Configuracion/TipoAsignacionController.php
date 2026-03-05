<?php

namespace App\Http\Controllers\SuperAdmin\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Configuracion\TipoAsignacionRequest;
use App\Services\SuperAdmin\Configuracion\TipoAsignacionService;
use Illuminate\Http\Request;

class TipoAsignacionController extends Controller
{
    protected $service;

    public function __construct(TipoAsignacionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $tipos = $this->service->paginate(5, $request->buscar);
        return view('superadmin.configuracion.tipo_asignacion.index', compact('tipos'));
    }

    public function store(TipoAsignacionRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->back()->with('success', 'Tipo de asignación creado correctamente.');
    }

    public function update(TipoAsignacionRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->back()->with('success', 'Tipo de asignación actualizado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        return $this->service->exportExcel($request->buscar);
    }
}
