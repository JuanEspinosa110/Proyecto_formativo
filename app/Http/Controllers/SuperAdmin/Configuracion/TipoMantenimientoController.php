<?php

namespace App\Http\Controllers\SuperAdmin\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Configuracion\TipoMantenimientoRequest;
use App\Services\SuperAdmin\Configuracion\TipoMantenimientoService;
use Illuminate\Http\Request;

class TipoMantenimientoController extends Controller
{
    protected $service;

    public function __construct(TipoMantenimientoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $tipos = $this->service->paginate(5, $request->buscar);
        return view('superadmin.configuracion.tipo_mantenimiento.index', compact('tipos'));
    }

    public function store(TipoMantenimientoRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->back()->with('success', 'Tipo de mantenimiento creado correctamente.');
    }

    public function update(TipoMantenimientoRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->back()->with('success', 'Tipo de mantenimiento actualizado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        return $this->service->exportExcel($request->buscar);
    }
}
