<?php

namespace App\Http\Controllers\SuperAdmin\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Configuracion\CiudadRequest;
use App\Services\SuperAdmin\Configuracion\CiudadService;
use Illuminate\Http\Request;

use App\Models\Ciudad;
use App\Models\Departamento;

class CiudadController extends Controller
{
    protected $service;

    public function __construct(CiudadService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        // Capturamos los filtros por separado
        $filtroCiudad = $request->get('filtro_ciudad');
        $filtroDepto = $request->get('filtro_depto');

        $ciudades = Ciudad::with('departamento')
            ->when($filtroCiudad, function ($query) use ($filtroCiudad) {
                // Busca por nombre o ID de ciudad
                return $query->where(function ($q) use ($filtroCiudad) {
                    $q->where('nombre_city', 'like', "%{$filtroCiudad}%")
                        ->orWhere('id_ciudad', 'like', "%{$filtroCiudad}%");
                });
            })
            ->when($filtroDepto, function ($query) use ($filtroDepto) {
                // Busca por nombre o ID de departamento dentro de la relación
                return $query->whereHas('departamento', function ($q) use ($filtroDepto) {
                    $q->where('nombre_departamento', 'like', "%{$filtroDepto}%")
                        ->orWhere('id_departamento', 'like', "%{$filtroDepto}%");
                });
            })
            ->orderBy('id_ciudad', 'asc')
            ->paginate(5);

        // mantenemos AMBOS filtros en la paginación
        $ciudades->appends([
            'filtro_ciudad' => $filtroCiudad,
            'filtro_depto' => $filtroDepto
        ]);

        $departamentos = Departamento::orderBy('nombre_departamento')->get();

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
        // 1. Validamos incluyendo el id_departamento
        $data = $request->validate([
            'id_departamento'     => 'required|string|size:2|unique:departamento,id_departamento',
            'nombre_departamento' => 'required|string|max:100|unique:departamento,nombre_departamento'
        ], [
            'id_departamento.unique' => 'Este código de departamento ya existe.',
            'id_departamento.size'   => 'El código debe ser de exactamente 2 dígitos.'
        ]);

        // 2. Ahora sí pasamos el array con las dos llaves al Service
        $this->service->storeDepartamento($data);

        return redirect()->back()->with('success', 'Departamento creado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        // Captura los filtros de la URL
        $filtroCiudad = $request->query('filtro_ciudad');
        $filtroDepto = $request->query('filtro_depto');

        return $this->service->exportExcel($filtroCiudad, $filtroDepto);
    }
}
