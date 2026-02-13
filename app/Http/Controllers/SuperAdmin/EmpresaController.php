<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Ciudad;
use App\Models\Departamento;
use App\Models\Estado;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Listado de empresas
     */
    public function index(Request $request)
    {
        $query = Empresa::with(['ciudad.departamento', 'estado']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nombre_empresa', 'like', "%$search%")
                  ->orWhere('NIT', 'like', "%$search%")
                  ->orWhere('correo_corporativo', 'like', "%$search%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }

        if ($request->filled('ciudad')) {
            $query->where('id_ciudad', $request->ciudad);
        }

        $empresas = $query->orderBy('fecha_creacion', 'desc')->paginate(10);
        $estados = Estado::whereIn('id_estado', [1,2,3,6])->get();
        $ciudades = Ciudad::orderBy('nombre_city')->get();

        return view('superadmin.empresas.index', compact('empresas','estados','ciudades'));
    }

    /**
     * Formulario crear empresa
     */
    public function create()
    {
        $departamentos = Departamento::orderBy('nombre_departamento')->get();
        $estados = Estado::whereIn('id_estado', [1,2,3,6])->get();

        return view('superadmin.empresas.create', compact('departamentos','estados'));
    }

    /**
     * Guardar empresa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            // EMPRESA
            'NIT' => 'required|numeric|unique:empresa,NIT',
            'nombre_empresa' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'telefono_empresa' => 'required|numeric',
            'correo_corporativo' => ['required','regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],

            // REPRESENTANTE
            'doc_representante' => 'required|numeric',
            'primer_nombre_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'segundo_nombre_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'primer_apellido_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'segundo_apellido_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'telefono_representante' => 'required|numeric',
            'correo_representante' => ['required','regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],

            // UBICACIÓN
            'id_ciudad' => 'required|exists:ciudad,id_ciudad',
            'id_estado' => 'required|exists:estado,id_estado',

        ], [
            'required' => 'Este campo es obligatorio.',
            'numeric' => 'Solo se permiten números.',
            'correo_corporativo.regex' => 'Debe ser un correo válido @gmail.com',
            'correo_representante.regex' => 'Debe ser un correo válido @gmail.com',
        ]);

        // Asignar tipo empresa automáticamente
        $validated['id_tipo_empresa'] = 1;

        Empresa::create($validated);

        return redirect()
            ->route('superadmin.empresas.index')
            ->with('success', 'La empresa ha sido creada exitosamente.');
    }

    /**
     * Editar empresa
     */
    public function edit($nit)
    {
        $empresa = Empresa::with('ciudad')->findOrFail($nit);

        $departamentos = Departamento::orderBy('nombre_departamento')->get();

        $ciudades = Ciudad::where('id_departamento', $empresa->ciudad->id_departamento)
            ->orderBy('nombre_city')
            ->get();

        $estados = Estado::whereIn('id_estado', [1,2,3,6])->get();

        return view('superadmin.empresas.edit', compact(
            'empresa',
            'departamentos',
            'ciudades',
            'estados'
        ));
    }


    /**
     * Actualizar empresa
     */
    public function update(Request $request, $nit)
    {
        $empresa = Empresa::findOrFail($nit);

        $validated = $request->validate([

            'nombre_empresa' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'doc_representante' => 'required|numeric',
            'primer_nombre_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'segundo_nombre_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'primer_apellido_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'segundo_apellido_repre' => ['required','regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
            'telefono_representante' => 'required|numeric',
            'telefono_empresa' => 'required|numeric',
            'correo_representante' => ['required','regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],
            'correo_corporativo' => ['required','regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],
            'id_ciudad' => 'required|exists:ciudad,id_ciudad',
            'id_estado' => 'required|exists:estado,id_estado',

        ]);

        $empresa->update($validated);

        return redirect()
            ->route('superadmin.empresas.index')
            ->with('success', 'Empresa actualizada correctamente.');
    }

    /**
     * Mostrar detalles
     */
    public function show($nit)
    {
        $empresa = Empresa::with(['ciudad.departamento','estado','usuarios'])
            ->findOrFail($nit);

        return view('superadmin.empresas.show', compact('empresa'));
    }

    /**
     * Cargar ciudades por departamento (AJAX)
     */
    public function getCiudadesByDepartamento($id_departamento)
    {
        $ciudades = Ciudad::where('id_departamento', $id_departamento)
            ->orderBy('nombre_city')
            ->get();

        return response()->json($ciudades);
    }
}
