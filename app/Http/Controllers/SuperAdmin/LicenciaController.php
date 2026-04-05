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

        // Aplicar filtro de estado (con lógica de fecha para consistencia)
        if ($request->filled('estado')) {
            $estadoSeleccionado = (int)$request->estado;
            if ($estadoSeleccionado === 1) { // ACTIVA
                $query->where('licencias.id_estado', 1)
                      ->where('licencias.fecha_vencimiento', '>=', now()->toDateString());
            } elseif ($estadoSeleccionado === 8) { // VENCIDA
                $query->where(function($q) {
                    $q->where('licencias.id_estado', 8)
                      ->orWhere(function($q2) {
                          $q2->where('licencias.id_estado', 1)
                             ->where('licencias.fecha_vencimiento', '<', now()->toDateString());
                      });
                });
            } else {
                $query->where('licencias.id_estado', $estadoSeleccionado);
            }
        }

        // Aplicar filtro de plan
        if ($request->filled('plan')) {
            $query->where('licencias.id_plan', $request->plan);
        }

        // Aplicar filtro rápido (tabs)
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'activas':
                    $query->where('licencias.id_estado', 1)
                        ->where('licencias.fecha_vencimiento', '>=', now()->toDateString());
                    break;
                case 'por_vencer':
                    $query->where('licencias.id_estado', 1)
                        ->whereRaw('DATEDIFF(licencias.fecha_vencimiento, CURDATE()) BETWEEN 0 AND 30');
                    break;
                case 'vencidas':
                    $query->where(function($q) {
                        $q->where('licencias.id_estado', 8)
                          ->orWhere(function($q2) {
                              $q2->where('licencias.id_estado', 1)
                                 ->where('licencias.fecha_vencimiento', '<', now()->toDateString());
                          });
                    });
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
            'activas' => $allLicencias->where('id_estado', 1)
                ->where('fecha_vencimiento', '>=', now()->toDateString())->count(),
            'proximas_vencer' => $allLicencias->filter(function ($lic) {
                if ($lic->id_estado != 1) return false;
                $diasRestantes = (int)Carbon::today()->diffInDays(Carbon::parse($lic->fecha_vencimiento), false);
                return $diasRestantes >= 0 && $diasRestantes <= 30;
            })->count(),
            'vencidas' => $allLicencias->filter(function ($lic) {
                return $lic->id_estado == 8 || ($lic->id_estado == 1 && Carbon::parse($lic->fecha_vencimiento)->lt(Carbon::today()));
            })->count(),
        ];

        // Obtener listas para filtros
        $estados = DB::table('estado')
            ->whereIn('id_estado', [1, 8]) // Solo Activa (1) y Vencida (8)
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
    public function create(Request $request)
    {
        $nit = $request->nit;
        $departamentos = DB::table('departamento')->orderBy('nombre_departamento')->get();
        return view('superadmin.licencias.crear_paso1', compact('departamentos', 'nit'));
    }

    /**
     * AJAX: Verificar NIT y obtener datos de empresa
     */
    public function verificarNit($nit)
    {
        // Verificar si el NIT existe
        $empresa = DB::table('empresa')
            ->join('ciudad', 'empresa.id_ciudad', '=', 'ciudad.id_ciudad')
            ->join('departamento', 'ciudad.id_departamento', '=', 'departamento.id_departamento')
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
            ->whereIn('id_estado', [1]) // VIGENTE/RENOVADA
            ->orderBy('fecha_vencimiento', 'desc')
            ->first();

        if ($licenciaActiva) {
            $fechaVencimiento = Carbon::parse($licenciaActiva->fecha_vencimiento);
            $diasRestantes = (int)Carbon::today()->diffInDays($fechaVencimiento, false);

            if ($diasRestantes > 30) {
                return response()->json([
                    'existe' => true,
                    'tiene_licencia' => true,
                    'error' => true,
                    'mensaje' => "Esta empresa ya tiene una licencia activa (ID: {$licenciaActiva->id_licencia}) que vence en {$diasRestantes} días. Solo se permite renovar cuando falten 30 días o menos."
                ]);
            }
            
            // Guardar info de la licencia actual para validaciones posteriores
            session(['licencia_actual' => [
                'id_licencia' => $licenciaActiva->id_licencia,
                'fecha_vencimiento' => $licenciaActiva->fecha_vencimiento,
                'dias_restantes' => $diasRestantes
            ]]);
        } else {
            session()->forget('licencia_actual');
        }

        // Guardar datos verificados en sesión
        // Esto será usado para validar que no haya cambios
        session([
            'nit_verificado' => $nit,
            'datos_verificados' => [
                'nombre_empresa' => $empresa->nombre_empresa,
                'telefono_empresa' => $empresa->telefono_empresa,
                'correo_corporativo' => $empresa->correo_corporativo,
                'id_departamento' => $empresa->id_departamento,
                'id_ciudad' => $empresa->id_ciudad,
            ]
        ]);

        // Buscar administrador existente (Rol ID 1) vinculado a la empresa
        $adminExistente = DB::table('usuario')
            ->where('NIT', $nit)
            ->where('id_tipo_usuario', 1)
            ->where('id_estado', 1)
            ->select('doc_usuario', 'primer_nombre', 'primer_apellido', 'correo')
            ->first();

        // Guardar estado en sesión para la validación del paso 1
        session(['admin_existente' => $adminExistente ? true : false]);

        // Empresa existe pero no tiene licencia activa
        return response()->json([
            'existe' => true,
            'tiene_licencia' => false,
            'admin_existente' => $adminExistente ? true : false,
            'nombre_admin' => $adminExistente ? ($adminExistente->primer_nombre . ' ' . $adminExistente->primer_apellido) : null,
            'correo_admin' => $adminExistente ? $adminExistente->correo : null,
            'datos' => [
                'nombre_empresa' => $empresa->nombre_empresa,
                'telefono_empresa' => $empresa->telefono_empresa,
                'correo_corporativo' => $empresa->correo_corporativo,
                'id_departamento' => $empresa->id_departamento,
                'nombre_departamento' => $empresa->nombre_departamento,
                'id_ciudad' => $empresa->id_ciudad,
                'nombre_city' => $empresa->nombre_city,
            ]
        ]);
    }

    /**
     * PASO 1: Guardar/Actualizar empresa y pasar al paso 2
     */
    public function guardarPaso1(Request $request)
    {
        // VALIDACIÓN 1: Verificar que exista NIT verificado en sesión
        if (!session('nit_verificado')) {
            return redirect()->back()
                ->withErrors(['NIT' => 'Debe verificar el NIT antes de continuar. Por favor, complete el proceso nuevamente.'])
                ->withInput();
        }

        // VALIDACIÓN 2: Verificar que el NIT no haya sido alterado
        $nitVerificado = session('nit_verificado');
        if ($request->NIT != $nitVerificado) {
            return redirect()->back()
                ->withErrors(['NIT' => 'El NIT ha sido modificado después de la verificación. Verifique el NIT nuevamente.'])
                ->withInput();
        }

        // VALIDACIÓN 3: Verificar datos de empresa no hayan sido alterados
        $datosVerificados = session('datos_verificados');
        $erroresIntegridad = [];

        if ($request->nombre_empresa !== $datosVerificados['nombre_empresa']) {
            $erroresIntegridad['nombre_empresa'] = 'El nombre de la empresa fue modificado. Verifique el NIT nuevamente.';
        }

        if ($request->telefono_empresa !== $datosVerificados['telefono_empresa']) {
            $erroresIntegridad['telefono_empresa'] = 'El teléfono de la empresa fue modificado. Verifique el NIT nuevamente.';
        }

        if ($request->correo_corporativo !== $datosVerificados['correo_corporativo']) {
            $erroresIntegridad['correo_corporativo'] = 'El correo corporativo fue modificado. Verifique el NIT nuevamente.';
        }

        if (!empty($erroresIntegridad)) {
            return redirect()->back()
                ->withErrors($erroresIntegridad)
                ->withInput();
        }

        // ✅ VALIDACIÓN 4: Verificar que departamento y ciudad correspondan con el NIT
        $empresa = DB::table('empresa')
            ->where('NIT', $nitVerificado)
            ->first();

        if ($request->id_departamento != $datosVerificados['id_departamento']) {
            return redirect()->back()
                ->withErrors(['id_departamento' => 'El departamento seleccionado no corresponde con el NIT verificado. Seleccione el departamento correcto.'])
                ->withInput();
        }

        if ($request->id_ciudad != $datosVerificados['id_ciudad']) {
            return redirect()->back()
                ->withErrors(['id_ciudad' => 'La ciudad seleccionada no corresponde con el NIT verificado. Seleccione la ciudad correcta.'])
                ->withInput();
        }

        // ✅ VALIDACIÓN 5: Verificar que la ciudad pertenece al departamento seleccionado
        $ciudadValida = DB::table('ciudad')
            ->where('id_ciudad', $request->id_ciudad)
            ->where('id_departamento', $request->id_departamento)
            ->exists();

        if (!$ciudadValida) {
            return redirect()->back()
                ->withErrors(['id_ciudad' => 'La ciudad seleccionada no pertenece al departamento indicado.'])
                ->withInput();
        }

        $adminExistente = session('admin_existente', false);

        // Ahora proceder con las validaciones normales
        $rules = [
            // Empresa
            'NIT' => 'required|numeric',
            'nombre_empresa' => 'required|string|max:150',
            'id_departamento' => 'required|exists:departamento,id_departamento',
            'id_ciudad' => 'required|exists:ciudad,id_ciudad',
            'telefono_empresa' => 'nullable|digits_between:7,15',
            'correo_corporativo' => 'required|email|max:150',
            'admin_existente_flag' => 'nullable|string'
        ];

        // Validaciones del administrador solo si no existe uno previo
        if (!$adminExistente) {
            $rules += [
                'doc_admin' => 'required|digits_between:8,10',
                'primer_nombre_admin' => ['required', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+$/'],
                'segundo_nombre_admin' => ['nullable', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
                'primer_apellido_admin' => ['required', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ]+$/'],
                'segundo_apellido_admin' => ['nullable', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/'],
                'telefono_admin' => 'nullable|digits_between:7,15',
                'correo_admin' => ['required', 'email', 'regex:/^.+@.+\..+$/', 'max:150'],
                'password_admin' => [
                    'required',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                ],
            ];
        }

        $validated = $request->validate($rules, [
            // Mensajes para Empresa
            'id_departamento.required' => 'Debe seleccionar un departamento.',
            'id_departamento.exists'   => 'El departamento seleccionado no es válido.',
            'id_ciudad.required' => 'Debe seleccionar una ciudad.',
            'id_ciudad.exists'   => 'La ciudad seleccionada no es válida.',

            // Mensajes para el Documento
            'doc_admin.required' => 'El documento de identidad es obligatorio.',
            'doc_admin.digits_between' => 'El documento debe tener entre 8 y 12 dígitos.',

            // Mensajes para nombres
            'primer_nombre_admin.required' => 'El primer nombre es obligatorio.',
            'primer_nombre_admin.regex' => 'El primer nombre solo debe contener letras.',
            'segundo_nombre_admin.regex' => 'El segundo nombre solo debe contener letras.',
            'primer_apellido_admin.required' => 'El primer apellido es obligatorio.',
            'primer_apellido_admin.regex' => 'El primer apellido solo debe contener letras.',
            'segundo_apellido_admin.regex' => 'El segundo apellido solo debe contener letras.',

            // Mensajes para teléfono
            'telefono_admin.digits_between' => 'El teléfono debe tener entre 7 y 15 dígitos.',

            // Mensajes para correo
            'correo_admin.required' => 'El correo electrónico es obligatorio.',
            'correo_admin.email' => 'El correo electrónico debe ser válido.',
            'correo_admin.regex' => 'El correo electrónico debe tener un formato válido.',

            // Mensajes para contraseña
            'password_admin.required' => 'La contraseña es obligatoria.',
            'password_admin.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password_admin.regex' => 'La contraseña debe contener mayúsculas, minúsculas y números.',
        ]);

        // ✅ Validaciones adicionales solo si es un administrador nuevo
        if (!$adminExistente) {
            $docAdminExiste = DB::table('usuario')
                ->where('doc_usuario', $validated['doc_admin'])
                ->exists();

            if ($docAdminExiste) {
                return redirect()->back()
                    ->withErrors(['doc_admin' => 'Este documento de identidad ya está registrado en el sistema.'])
                    ->withInput();
            }

            $correoAdminExiste = DB::table('usuario')
                ->where('correo', $validated['correo_admin'])
                ->exists();

            if ($correoAdminExiste) {
                return redirect()->back()
                    ->withErrors(['correo_admin' => 'Este correo electrónico ya está registrado como administrador en el sistema.'])
                    ->withInput();
            }
        }
        try {
            DB::beginTransaction();

            $empresaExiste = DB::table('empresa')->where('NIT', $validated['NIT'])->exists();

            // Datos básicos de la empresa a insertar/actualizar
            $datosEmpresa = [
                'nombre_empresa' => $validated['nombre_empresa'],
                'telefono_empresa' => $validated['telefono_empresa'],
                'correo_corporativo' => $validated['correo_corporativo'],
                'id_ciudad' => $validated['id_ciudad'],
            ];

            if ($empresaExiste) {
                DB::table('empresa')->where('NIT', $validated['NIT'])->update($datosEmpresa);
            } else {
                $datosEmpresa['NIT'] = $validated['NIT'];
                $datosEmpresa['id_estado'] = 1;
                $datosEmpresa['fecha_creacion'] = now();
                DB::table('empresa')->insert($datosEmpresa);
            }

            // Usuario Administrador (solo si no existe uno previo)
            if (!$adminExistente) {
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
                    'id_tipo_usuario' => 1,
                    'id_ciudad' => $validated['id_ciudad'],
                    'id_estado' => 1,
                ]);
            }

            DB::commit();

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
                ->with('error', 'Error al procesar los datos: ' . $e->getMessage());
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

        // Validación adicional si existe una licencia previa
        if (session('licencia_actual')) {
            $licPrev = session('licencia_actual');
            $fechaMinimaPermitida = Carbon::parse($licPrev['fecha_vencimiento'])->addDay();

            if (Carbon::parse($validated['fecha_inicio'])->lt($fechaMinimaPermitida)) {
                return back()->withInput()->withErrors([
                    'fecha_inicio' => 'La fecha de inicio para la nueva licencia debe ser como mínimo el día siguiente al vencimiento de la actual (' . $fechaMinimaPermitida->format('d/m/Y') . ').'
                ]);
            }
        }

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
        $estados = DB::table('estado')->whereIn('id_estado', [1, 8])->get();
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
        $estados = DB::table('estado')->whereIn('id_estado', [1, 8, 9])->get();
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
            DB::table('licencias')->where('id_licencia', $id)->update(['fecha_vencimiento' => $validated['nueva_fecha'], 'id_estado' => 1]);
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
     * Exportar licencias a CSV respetando filtros
     */
    public function export(Request $request)
    {
        // Construir query base (igual que en index)
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
                'planes_licencia.duracion_meses',
                'estado.nombre_estado'
            );

        // Aplicar los MISMOS filtros que en index
        $query = $this->applyFilters($query, $request);

        // Obtener todas las licencias filtradas (sin paginación)
        $licencias = $query->orderBy('licencias.fecha_creacion', 'desc')->get();

        // Generar nombre de archivo
        $filters = [];
        if ($request->filled('filter')) $filters[] = $request->filter;
        if ($request->filled('search')) $filters[] = 'busqueda';
        if ($request->filled('estado')) $filters[] = 'estado';
        if ($request->filled('plan')) $filters[] = 'plan';

        $filterSuffix = !empty($filters) ? '_' . implode('_', $filters) : '';
        $filename = 'licencias' . $filterSuffix . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = [
            'ID Licencia',
            'NIT',
            'Empresa',
            'Correo Corporativo',
            'Plan',
            'Duración (meses)',
            'Precio',
            'Estado',
            'Fecha Inicio',
            'Fecha Vencimiento',
            'Días Restantes',
            'Fecha Creación'
        ];

        $callback = function () use ($licencias, $columns) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8 (para Excel)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Encabezados
            fputcsv($file, $columns);

            // Datos
            foreach ($licencias as $lic) {
                $diasRestantes = (int) Carbon::today()->diffInDays(Carbon::parse($lic->fecha_vencimiento), false);

                // Estado de días restantes
                if ($diasRestantes < 0) {
                    $diasRestantesTexto = 'VENCIDA';
                } else {
                    $diasRestantesTexto = $diasRestantes . ' días';
                }

                $row = [
                    $lic->id_licencia,
                    $lic->NIT,
                    $lic->nombre_empresa,
                    $lic->correo_corporativo,
                    $lic->nombre_plan,
                    $lic->duracion_meses,
                    number_format($lic->precio, 2, '.', ''),
                    $lic->nombre_estado,
                    Carbon::parse($lic->fecha_inicio)->format('Y-m-d'),
                    Carbon::parse($lic->fecha_vencimiento)->format('Y-m-d'),
                    $diasRestantesTexto,
                    Carbon::parse($lic->fecha_creacion)->format('Y-m-d H:i:s'),
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar licencias a Excel respetando filtros
     */
    public function exportExcel(Request $request)
    {
        // Construir query base (igual que en index)
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
                'planes_licencia.duracion_meses',
                'estado.nombre_estado'
            );

        // Aplicar los MISMOS filtros que en index
        $query = $this->applyFilters($query, $request);

        // Obtener todas las licencias filtradas (sin paginación)
        $licencias = $query->orderBy('licencias.fecha_creacion', 'desc')->get();

        // Generar nombre de archivo
        $filters = [];
        if ($request->filled('filter')) $filters[] = $request->filter;
        if ($request->filled('search')) $filters[] = 'busqueda';
        if ($request->filled('estado')) $filters[] = 'estado';
        if ($request->filled('plan')) $filters[] = 'plan';

        $filterSuffix = !empty($filters) ? '_' . implode('_', $filters) : '';
        $filename = 'licencias' . $filterSuffix . '_' . now()->format('Ymd_His') . '.xlsx';

        // Crear spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Establecer encabezados
        $headers = [
            'A' => 'ID Licencia',
            'B' => 'NIT',
            'C' => 'Empresa',
            'D' => 'Correo Corporativo',
            'E' => 'Plan',
            'F' => 'Duración (meses)',
            'G' => 'Precio',
            'H' => 'Estado',
            'I' => 'Fecha Inicio',
            'J' => 'Fecha Vencimiento',
            'K' => 'Días Restantes',
            'L' => 'Fecha Creación'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // Agregar datos
        $row = 2;
        foreach ($licencias as $lic) {
            $diasRestantes = (int) Carbon::today()->diffInDays(Carbon::parse($lic->fecha_vencimiento), false);
            $diasRestantesTexto = $diasRestantes < 0 ? 'VENCIDA' : $diasRestantes . ' días';

            $sheet->setCellValue('A' . $row, $lic->id_licencia);
            $sheet->setCellValue('B' . $row, $lic->NIT);
            $sheet->setCellValue('C' . $row, $lic->nombre_empresa);
            $sheet->setCellValue('D' . $row, $lic->correo_corporativo);
            $sheet->setCellValue('E' . $row, $lic->nombre_plan);
            $sheet->setCellValue('F' . $row, $lic->duracion_meses);
            $sheet->setCellValue('G' . $row, $lic->precio);
            $sheet->setCellValue('H' . $row, $lic->nombre_estado);
            $sheet->setCellValue('I' . $row, Carbon::parse($lic->fecha_inicio)->format('Y-m-d'));
            $sheet->setCellValue('J' . $row, Carbon::parse($lic->fecha_vencimiento)->format('Y-m-d'));
            $sheet->setCellValue('K' . $row, $diasRestantesTexto);
            $sheet->setCellValue('L' . $row, Carbon::parse($lic->fecha_creacion)->format('Y-m-d H:i:s'));

            $row++;
        }

        // Ajustar anchos de columna
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);

        // Guardar y descargar
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }


    /**
     * Aplicar filtros a la consulta (método compartido)
     */
    private function applyFilters($query, Request $request)
    {
        // Filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('empresa.nombre_empresa', 'LIKE', "%{$search}%")
                    ->orWhere('licencias.NIT', 'LIKE', "%{$search}%")
                    ->orWhere('licencias.id_licencia', 'LIKE', "%{$search}%");
            });
        }

        // Filtro de estado
        if ($request->filled('estado')) {
            $query->where('licencias.id_estado', $request->estado);
        }

        // Filtro de plan
        if ($request->filled('plan')) {
            $query->where('licencias.id_plan', $request->plan);
        }

        // Filtros rápidos (tabs)
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
                    $query->where('licencias.id_estado', 8);
                    break;
            }
        }

        return $query;
    }

    /**
     * AJAX: Obtener detalles completos de una licencia
     */
    public function getDetalles($id_licencia)
    {
        try {
            // 1. Obtener el NIT de la licencia (El id_licencia es char(14))
            $licencia = DB::table('licencias')
                ->where('id_licencia', $id_licencia)
                ->first();

            if (!$licencia) {
                return response()->json(['error' => 'Licencia no encontrada'], 404);
            }

            // 2. Obtener representante legal (Tabla: empresa)
            // Las columnas aquí coinciden con tu SQL: primer_nombre_repre, etc.
            $representante = DB::table('empresa')
                ->where('NIT', $licencia->NIT)
                ->select(
                    DB::raw("CONCAT(COALESCE(primer_nombre_repre, ''), ' ', COALESCE(segundo_nombre_repre, ''), ' ', COALESCE(primer_apellido_repre, ''), ' ', COALESCE(segundo_apellido_repre, '')) as nombre_completo"),
                    'doc_representante',
                    'telefono_representante',
                    'correo_representante'
                )
                ->first();

            // 3. Obtener administrador (Tabla: usuario)
            $admin = DB::table('usuario')
                ->where('NIT', $licencia->NIT)
                ->where('id_tipo_usuario', 1) // Filtramos por tipo Admin
                ->select(
                    DB::raw("CONCAT(COALESCE(primer_nombre, ''), ' ', COALESCE(segundo_nombre, ''), ' ', COALESCE(primer_apellido, ''), ' ', COALESCE(segundo_apellido, '')) as nombre_completo"),
                    'doc_usuario as doc_admin',
                    'telefono as telefono_admin',
                    'correo as correo_admin'
                )
                ->first();

            return response()->json([
                'representante' => $representante,
                'admin' => $admin,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno del servidor',
                'detalle' => $e->getMessage()
            ], 500);
        }
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
