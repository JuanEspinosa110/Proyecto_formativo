<?php

namespace App\Http\Controllers\SuperAdmin\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Configuracion\CiudadRequest;
use App\Services\SuperAdmin\Configuracion\CiudadService;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    protected $service;

    public function __construct(CiudadService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $ciudades = $this->service->paginate(4, $request->buscar);
        $departamentos = $this->service->getDepartamentos();
        return view('superadmin.configuracion.ciudades.index', compact('ciudades', 'departamentos'));
    }

    public function store(CiudadRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->back()->with('success', 'Ciudad creada correctamente.');
    }

    public function update(CiudadRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->back()->with('success', 'Ciudad actualizada correctamente.');
    }

    public function storeDepartamento(Request $request)
    {
        $data = $request->validate([
            'nombre_departamento' => 'required|string|max:100'
        ]);

        $this->service->storeDepartamento($data);
        return redirect()->back()->with('success', 'Departamento creado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        return $this->service->exportExcel($request->buscar);
    }
}
