<?php

namespace App\Http\Controllers\SuperAdmin\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Configuracion\TipoUsuarioRequest;
use App\Services\SuperAdmin\Configuracion\TipoUsuarioService;
use Illuminate\Http\Request;

class TipoUsuarioController extends Controller
{
    protected $service;

    public function __construct(TipoUsuarioService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $tipos = $this->service->paginate(5, $request->buscar);
        return view('superadmin.configuracion.tipo_usuario.index', compact('tipos'));
    }

    public function store(TipoUsuarioRequest $request)
    {
        $this->service->store($request->validated());
        return redirect()->back()->with('success', 'Tipo de usuario creado correctamente.');
    }

    public function update(TipoUsuarioRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return redirect()->back()->with('success', 'Tipo de usuario actualizado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        return $this->service->exportExcel($request->buscar);
    }
}
