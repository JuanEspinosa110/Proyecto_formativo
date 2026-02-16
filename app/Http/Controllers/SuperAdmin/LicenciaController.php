<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LicenciaController extends Controller
{
    /**
     * Index con filtros del lado del servidor
     */
    public function index(Request $request)
    {
        // Construir query base
        $query = DB::table('licencias')
            ->join('empresa', 'licencias.NIT', '=', 'empresa.NIT')
            ->join('planes_licencia', 'licencias.id_plan', '=', 'planes_licencia.id_plan')
            ->join('estado', 'licencias.id_estado', '=', 'estado.id_estado')
            ->select(
                'licencias.*',
                'empresa.nombre_empresa',
                'empresa.correo_corporativo',
                'planes_licencia.nombre_plan',
                'planes_licencia.precio',
                'estado.nombre_estado'
            );

        // Aplicar filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('empresa.nombre_empresa', 'LIKE', "%{$search}%")
                    ->orWhere('licencias.NIT', 'LIKE', "%{$search}%")
                    ->orWhere('licencias.id_licencia', 'LIKE', "%{$search}%");
            });
        }

        // Aplicar filtro de estado
        if ($request->filled('estado')) {
            $query->where('licencias.id_estado', $request->estado);
        }

        // Aplicar filtro de plan
        if ($request->filled('plan')) {
            $query->where('licencias.id_plan', $request->plan);
        }

        // Aplicar filtro rápido (tabs)
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'activas':
                    $query->where('licencias.id_estado', 1);
                    break;
                case 'por_vencer':
                    $query->where('licencias.id_estado', 1)
                        ->whereRaw('DATEDIFF(licencias.fecha_vencimiento, CURDATE()) BETWEEN 0 AND 30');
                    break;
                case 'vencidas':
                    $query->where('licencias.id_estado', 21);
                    break;
            }
        }

        // Ordenar y paginar (manteniendo filtros en la URL)
        $licencias = $query->orderBy('licencias.fecha_creacion', 'desc')
            ->paginate(5)
            ->appends($request->except('page'));

        // Estadísticas globales (sin filtros)
        $allLicencias = DB::table('licencias')
            ->join('estado', 'licencias.id_estado', '=', 'estado.id_estado')
            ->select('licencias.*', 'estado.nombre_estado')
            ->get();

        $stats = [
            'total' => $allLicencias->count(),
            'activas' => $allLicencias->where('id_estado', 1)->count(),
            'proximas_vencer' => $allLicencias->filter(function ($lic) {
                if ($lic->id_estado != 1) return false;
                $diasRestantes = (int)Carbon::today()->diffInDays(Carbon::parse($lic->fecha_vencimiento), false);
                return $diasRestantes >= 0 && $diasRestantes <= 30;
            })->count(),
            'vencidas' => $allLicencias->where('id_estado', 21)->count(),
        ];

        // Obtener listas para filtros
        $estados = DB::table('estado')
            ->whereIn('id_estado', [1, 3, 21, 22]) // Solo estados relevantes
            ->orderBy('nombre_estado')
            ->get();

        $planes = DB::table('planes_licencia')
            ->where('id_estado', 1)
            ->orderBy('nombre_plan')
            ->get();

        return view('superadmin.licencias.index', compact('licencias', 'stats', 'estados', 'planes'));
    }

    /**
     * PASO 1: Formulario inicial (Empresa + Representante + Admin)
     */
    public function create()
    {
        $departamentos = DB::table('departamento')->orderBy('nombre_departamento')->get();
        return view('superadmin.licencias.crear_paso1', compact('departamentos'));
    }

    /**
     * AJAX: Verificar NIT y obtener datos de empresa
     */
    public function verificarNit($nit)
    {
        // Verificar si el NIT existe
        $empresa = DB::table('empresa')
            ->leftJoin('ciudad', 'empresa.id_ciudad', '=', 'ciudad.id_ciudad')
            ->leftJoin('departamento', 'ciudad.id_departamento', '=', 'departamento.id_departamento')
            ->where('empresa.NIT', $nit)
            ->select(
                'empresa.*',
                'ciudad.nombre_city',
                'ciudad.id_departamento',
                'departamento.nombre_departamento'
            )
            ->first();

        if (!$empresa) {
            return response()->json([
                'existe' => false,
                'mensaje' => 'NIT no registrado. Complete los datos para crear la empresa.'
            ]);
        }

        // Verificar si ya tiene licencia activa
        $licenciaActiva = DB::table('licencias')
            ->where('NIT', $nit)
            ->whereIn('id_estado', [1, 22]) // VIGENTE o RENOVADA
            ->first();

        if ($licenciaActiva) {
            return response()->json([
                'existe' => true,
                'tiene_licencia' => true,
                'error' => true,
                'mensaje' => 'Esta empresa ya tiene una licencia activa. ID: ' . $licenciaActiva->id_licencia
            ]);
        }

        // Empresa existe pero no tiene licencia activa
        return response()->json([
            'existe' => true,
            'tiene_licencia' => false,
            'datos' => [
                'nombre_empresa' => $empresa->nombre_empresa,
                'telefono_empresa' => $empresa->telefono_empresa,
                'correo_corporativo' => $empresa->correo_corporativo,
                'id_departamento' => $empresa->id_departamento,
                'nombre_departamento' => $empresa->nombre_departamento,
                'id_ciudad' => $empresa->id_ciudad,
                'nombre_city' => $empresa->nombre_city,
                // Datos del representante
                'doc_representante' => $empresa->doc_representante,
                'primer_nombre_repre' => $empresa->primer_nombre_repre,
                'segundo_nombre_repre' => $empresa->segundo_nombre_repre,
                'primer_apellido_repre' => $empresa->primer_apellido_repre,
                'segundo_apellido_repre' => $empresa->segundo_apellido_repre,
                'telefono_representante' => $empresa->telefono_representante,
                'correo_representante' => $empresa->correo_representante,
            ]
        ]);
    }

    /**
     * PASO 1: Guardar/Actualizar empresa y pasar al paso 2
     */
    public function guardarPaso1(Request $request)
    {
        $validated = $request->validate(
            [
                // Empresa
                'NIT' => 'required|numeric',
                'nombre_empresa' => 'required|string|max:150',
                'id_departamento' => 'required|exists:departamento,id_departamento',
                'id_ciudad' => 'required|exists:ciudad,id_ciudad',
                'telefono_empresa' => 'nullable|string|max:20',
                'correo_corporativo' => 'required|email|max:150',

                // Representante Legal
                'doc_representante' => 'required|numeric',
                'primer_nombre_repre' => 'required|string|max:50',
                'segundo_nombre_repre' => 'nullable|string|max:50',
                'primer_apellido_repre' => 'required|string|max:50',
                'segundo_apellido_repre' => 'nullable|string|max:50',
                'telefono_representante' => 'nullable|string|max:20',
                'correo_representante' => 'required|email|max:150',

                // Usuario Administrador
                'doc_admin' => 'required|numeric',
                'primer_nombre_admin' => 'required|string|max:50',
                'segundo_nombre_admin' => 'nullable|string|max:50',
                'primer_apellido_admin' => 'required|string|max:50',
                'segundo_apellido_admin' => 'nullable|string|max:50',
                'telefono_admin' => 'nullable|string|max:20',
                'correo_admin' => 'required|email|max:150',
                'password_admin' => 'required|min:8',
            ],
            [
                'NIT.required' => 'El NIT es obligatorio',
                'NIT.numeric' => 'El NIT debe ser un número válido',
                'nombre_empresa.required' => 'El nombre de la empresa es obligatorio',
                'correo_corporativo.required' => 'El correo corporativo es obligatorio',
                'correo_corporativo.email' => 'El correo corporativo debe ser un email válido',
                'doc_representante.required' => 'El documento del representante es obligatorio',
                'primer_nombre_repre.required' => 'El primer nombre del representante es obligatorio',
                'primer_apellido_repre.required' => 'El primer apellido del representante es obligatorio',
                'correo_representante.required' => 'El correo del representante es obligatorio',
                'correo_representante.email' => 'El correo del representante debe ser válido',
                'doc_admin.required' => 'El documento del administrador es obligatorio',
                'primer_nombre_admin.required' => 'El primer nombre del administrador es obligatorio',
                'primer_apellido_admin.required' => 'El primer apellido del administrador es obligatorio',
                'correo_admin.required' => 'El correo del administrador es obligatorio',
                'correo_admin.email' => 'El correo del administrador debe ser válido',
                'password_admin.required' => 'La contraseña es obligatoria',
                'password_admin.min' => 'La contraseña debe tener al menos 8 caracteres',
                'id_ciudad.required' => 'La ciudad es obligatoria',
            ]
        );

        try {
            DB::beginTransaction();

            $empresaExiste = DB::table('empresa')->where('NIT', $validated['NIT'])->exists();

            if ($empresaExiste) {
                // Actualizar datos de la empresa
                DB::table('empresa')
                    ->where('NIT', $validated['NIT'])
                    ->update([
                        'nombre_empresa' => $validated['nombre_empresa'],
                        'doc_representante' => $validated['doc_representante'],
                        'primer_nombre_repre' => $validated['primer_nombre_repre'],
                        'segundo_nombre_repre' => $validated['segundo_nombre_repre'],
                        'primer_apellido_repre' => $validated['primer_apellido_repre'],
                        'segundo_apellido_repre' => $validated['segundo_apellido_repre'],
                        'telefono_representante' => $validated['telefono_representante'],
                        'correo_representante' => $validated['correo_representante'],
                        'telefono_empresa' => $validated['telefono_empresa'],
                        'correo_corporativo' => $validated['correo_corporativo'],
                        'id_ciudad' => $validated['id_ciudad'],
                    ]);
            } else {
                // Crear nueva empresa
                DB::table('empresa')->insert([
                    'NIT' => $validated['NIT'],
                    'nombre_empresa' => $validated['nombre_empresa'],
                    'doc_representante' => $validated['doc_representante'],
                    'primer_nombre_repre' => $validated['primer_nombre_repre'],
                    'segundo_nombre_repre' => $validated['segundo_nombre_repre'],
                    'primer_apellido_repre' => $validated['primer_apellido_repre'],
                    'segundo_apellido_repre' => $validated['segundo_apellido_repre'],
                    'telefono_representante' => $validated['telefono_representante'],
                    'correo_representante' => $validated['correo_representante'],
                    'telefono_empresa' => $validated['telefono_empresa'],
                    'correo_corporativo' => $validated['correo_corporativo'],
                    'id_ciudad' => $validated['id_ciudad'],
                    'id_estado' => 1,
                    'fecha_creacion' => now(),
                ]);
            }

            // Verificar/Crear usuario administrador
            $usuarioExiste = DB::table('usuario')->where('doc_usuario', $validated['doc_admin'])->exists();

            if (!$usuarioExiste) {
                DB::table('usuario')->insert([
                    'doc_usuario' => $validated['doc_admin'],
                    'NIT' => $validated['NIT'],
                    'primer_nombre' => $validated['primer_nombre_admin'],
                    'segundo_nombre' => $validated['segundo_nombre_admin'],
                    'primer_apellido' => $validated['primer_apellido_admin'],
                    'segundo_apellido' => $validated['segundo_apellido_admin'],
                    'correo' => $validated['correo_admin'],
                    'password' => Hash::make($validated['password_admin']),
                    'telefono' => $validated['telefono_admin'],
                    'id_tipo_usuario' => 1, // Admin de empresa
                    'id_ciudad' => $validated['id_ciudad'],
                    'id_estado' => 1,
                ]);
            }

            DB::commit();

            // Guardar en sesión para el paso 2
            session([
                'licencia_paso1' => [
                    'NIT' => $validated['NIT'],
                    'nombre_empresa' => $validated['nombre_empresa'],
                ]
            ]);

            return redirect()->route('superadmin.licencias.crear-paso2');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * PASO 2: Seleccionar plan de licencia
     */
    public function crearPaso2()
    {
        if (!session('licencia_paso1')) {
            return redirect()->route('superadmin.licencias.create')
                ->with('error', 'Debe completar el paso 1 primero');
        }

        $datos = session('licencia_paso1');
        $planes = DB::table('planes_licencia')->where('id_estado', 1)->get();

        return view('superadmin.licencias.crear_paso2', compact('datos', 'planes'));
    }

    /**
     * PASO 2: Crear licencia final
     */
    public function store(Request $request)
    {
        if (!session('licencia_paso1')) {
            return redirect()->route('superadmin.licencias.create')
                ->with('error', 'Sesión expirada. Inicie nuevamente.');
        }

        $validated = $request->validate(
            [
                'id_plan' => 'required|exists:planes_licencia,id_plan',
                'fecha_inicio' => 'required|date|date_format:Y-m-d|after_or_equal:today',
                'fecha_vencimiento' => 'required|date|date_format:Y-m-d|after:fecha_inicio',
            ],
            [
                'id_plan.required' => 'Debe seleccionar un plan',
                'id_plan.exists' => 'El plan seleccionado no es válido',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
                'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
                'fecha_inicio.date_format' => 'La fecha de inicio debe estar en formato YYYY-MM-DD',
                'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser menor a la de hoy',
                'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria',
                'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida',
                'fecha_vencimiento.date_format' => 'La fecha de vencimiento debe estar en formato YYYY-MM-DD',
                'fecha_vencimiento.after' => 'La fecha de vencimiento debe ser posterior a la fecha de inicio',
            ]
        );

        try {
            $datos = session('licencia_paso1');
            $id_licencia = $this->generateLicenseId();
            $superAdminDoc = session('login_superadmin_59ba36addc2b2f9401580f014c7f58ea4e30989d');

            DB::table('licencias')->insert([
                'id_licencia' => $id_licencia,
                'NIT' => $datos['NIT'],
                'id_plan' => $validated['id_plan'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
                'id_estado' => 1, // VIGENTE
                'doc_super_admin' => $superAdminDoc,
                'fecha_creacion' => now(),
            ]);

            // Limpiar sesión
            session()->forget('licencia_paso1');

            return redirect()
                ->route('superadmin.licencias.index')
                ->with('success', 'Licencia creada exitosamente. ID: ' . $id_licencia);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear licencia: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $licencia = DB::table('licencias')->join('empresa', 'licencias.NIT', '=', 'empresa.NIT')
            ->join('planes_licencia', 'licencias.id_plan', '=', 'planes_licencia.id_plan')
            ->join('estado', 'licencias.id_estado', '=', 'estado.id_estado')
            ->where('licencias.id_licencia', $id)
            ->select('licencias.*', 'empresa.nombre_empresa', 'planes_licencia.nombre_plan as plan_nombre', 'estado.nombre_estado')
            ->first();
        if (!$licencia) return redirect()->route('superadmin.licencias.index')->with('error', 'Licencia no encontrada');
        $planes = DB::table('planes_licencia')->where('id_estado', 1)->get();
        $estados = DB::table('estado')->whereIn('id_estado', [1, 3, 21, 22])->get();
        return view('superadmin.licencias.editar', compact('licencia', 'planes', 'estados'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_plan' => 'required|exists:planes_licencia,id_plan',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_vencimiento' => 'required|date|after:fecha_inicio',
            'id_estado' => 'required|exists:estado,id_estado',
        ]);
        try {
            DB::table('licencias')->where('id_licencia', $id)->update($validated);
            return redirect()->route('superadmin.licencias.index')->with('success', 'Licencia actualizada');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function gestionarEstado($id)
    {
        $licencia = DB::table('licencias')->join('empresa', 'licencias.NIT', '=', 'empresa.NIT')
            ->join('planes_licencia', 'licencias.id_plan', '=', 'planes_licencia.id_plan')
            ->join('estado', 'licencias.id_estado', '=', 'estado.id_estado')
            ->where('licencias.id_licencia', $id)
            ->select('licencias.*', 'empresa.nombre_empresa', 'planes_licencia.nombre_plan', 'estado.nombre_estado')
            ->first();
        if (!$licencia) return redirect()->route('superadmin.licencias.index')->with('error', 'No encontrada');
        $estados = DB::table('estado')->whereIn('id_estado', [1, 3, 21, 14])->get();
        return view('superadmin.licencias.gestionar_estado', compact('licencia', 'estados'));
    }

    public function actualizarEstado(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'id_estado' => 'required|exists:estado,id_estado',
                'motivo' => 'required|string|min:20|max:500'
            ],
            [
                'id_estado.required' => 'Debe seleccionar un estado',
                'id_estado.exists' => 'El estado seleccionado no es válido',
                'motivo.required' => 'El motivo de la acción es obligatorio',
                'motivo.min' => 'El motivo debe tener al menos 20 caracteres',
                'motivo.max' => 'El motivo no puede exceder 500 caracteres'
            ]
        );
        try {
            DB::table('licencias')->where('id_licencia', $id)->update(['id_estado' => $validated['id_estado']]);
            return redirect()->route('superadmin.licencias.index')->with('success', 'Estado actualizado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    public function renovar($id)
    {
        $licencia = DB::table('licencias')->join('empresa', 'licencias.NIT', '=', 'empresa.NIT')
            ->join('planes_licencia', 'licencias.id_plan', '=', 'planes_licencia.id_plan')
            ->where('licencias.id_licencia', $id)
            ->select('licencias.*', 'empresa.nombre_empresa', 'planes_licencia.nombre_plan')
            ->first();
        if (!$licencia) return redirect()->route('superadmin.licencias.index')->with('error', 'No encontrada');
        return view('superadmin.licencias.renovar', compact('licencia'));
    }

    public function procesarRenovacion(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'nueva_fecha' => 'required|date|after:today',
                'notas' => 'nullable|string|max:500'
            ],
            [
                'nueva_fecha.required' => 'La nueva fecha de vencimiento es obligatoria',
                'nueva_fecha.date' => 'La fecha debe ser una fecha válida',
                'nueva_fecha.after' => 'La fecha debe ser posterior a hoy',
                'notas.max' => 'Las notas no pueden exceder 500 caracteres'
            ]
        );
        try {
            DB::table('licencias')->where('id_licencia', $id)->update(['fecha_vencimiento' => $validated['nueva_fecha'], 'id_estado' => 22]);
            return redirect()->route('superadmin.licencias.index')->with('success', 'Licencia renovada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al renovar la licencia: ' . $e->getMessage());
        }
    }

    public function historial()
    {
        $licencias = DB::table('licencias')->join('empresa', 'licencias.NIT', '=', 'empresa.NIT')
            ->join('planes_licencia', 'licencias.id_plan', '=', 'planes_licencia.id_plan')
            ->join('estado', 'licencias.id_estado', '=', 'estado.id_estado')
            ->select('licencias.*', 'empresa.nombre_empresa', 'planes_licencia.nombre_plan', 'estado.nombre_estado')
            ->orderBy('licencias.fecha_creacion', 'desc')->get();
        return view('superadmin.licencias.historial', compact('licencias'));
    }

    /**
     * Exportar listado de licencias como CSV
     */
    public function export()
    {
        $licencias = DB::table('licencias')
            ->join('empresa', 'licencias.NIT', '=', 'empresa.NIT')
            ->join('planes_licencia', 'licencias.id_plan', '=', 'planes_licencia.id_plan')
            ->join('estado', 'licencias.id_estado', '=', 'estado.id_estado')
            ->select(
                'licencias.*',
                'empresa.nombre_empresa',
                'empresa.correo_corporativo',
                'planes_licencia.nombre_plan',
                'planes_licencia.precio',
                'estado.nombre_estado'
            )
            ->orderBy('licencias.fecha_creacion', 'desc')
            ->get();

        $filename = 'licencias_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = [
            'ID Licencia',
            'NIT',
            'Empresa',
            'Correo Corporativo',
            'Plan',
            'Precio',
            'Estado',
            'Fecha Inicio',
            'Fecha Vencimiento',
            'Fecha Creación',
            'Días Restantes'
        ];

        $callback = function () use ($licencias, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($licencias as $lic) {
                $diasRestantes = (int) Carbon::today()->diffInDays(Carbon::parse($lic->fecha_vencimiento), false);

                $row = [
                    $lic->id_licencia,
                    $lic->NIT,
                    $lic->nombre_empresa,
                    $lic->correo_corporativo,
                    $lic->nombre_plan,
                    $lic->precio,
                    $lic->nombre_estado,
                    Carbon::parse($lic->fecha_inicio)->format('Y-m-d'),
                    Carbon::parse($lic->fecha_vencimiento)->format('Y-m-d'),
                    Carbon::parse($lic->fecha_creacion)->format('Y-m-d H:i'),
                    $diasRestantes,
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Obtener datos del plan de licencia (AJAX)
     */
    public function getPlanData($id_plan)
    {
        $plan = DB::table('planes_licencia')
            ->where('id_plan', $id_plan)
            ->where('id_estado', 1)
            ->first();

        if (!$plan) {
            return response()->json(['error' => 'Plan no encontrado'], 404);
        }

        return response()->json([
            'id_plan' => $plan->id_plan,
            'nombre_plan' => $plan->nombre_plan,
            'duracion_meses' => $plan->duracion_meses,
            'precio' => $plan->precio,
        ]);
    }

    /**
     * Obtener ciudades por departamento (AJAX)
     */
    public function getCiudades($id_departamento)
    {
        $ciudades = DB::table('ciudad')
            ->where('id_departamento', $id_departamento)
            ->orderBy('nombre_city')
            ->get();

        return response()->json($ciudades);
    }

    private function generateLicenseId()
    {
        do {
            $id = 'LIC' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 11));
        } while (DB::table('licencias')->where('id_licencia', $id)->exists());
        return $id;
    }
}
