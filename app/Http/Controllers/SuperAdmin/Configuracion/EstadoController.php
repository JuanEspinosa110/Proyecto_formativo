<?php

namespace App\Http\Controllers\SuperAdmin\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Configuracion\EstadoRequest;
use App\Services\SuperAdmin\Configuracion\EstadoService;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    protected $service;

    public function __construct(EstadoService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $estados = $this->service->paginate(5, $request->buscar);
        return view('superadmin.configuracion.estados.index', compact('estados'));
    }

    public function store(EstadoRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->back()->with('success', 'Estado creado correctamente.');
    }

    public function update(EstadoRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        return $this->service->exportExcel($request->buscar);
    }
}
