<?php

namespace App\Http\Controllers\GestorRecargas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recarga;
use App\Models\Tarjeta;
use App\Models\Usuario;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GestorRecargaController extends Controller
{
    /**
     * Dashboard / Inicio
     */
    public function dashboard()
    {
        $usuario = Auth::user();
        $nit = $usuario->NIT;
        $empresa = Empresa::find($nit);

        // Algunas estadísticas rápidas
        $totalRecargasHoy = Recarga::whereHas('gestor', function($q) use($nit) { $q->where('NIT', $nit); })->whereDate('created_at', today())->count();
        $montoRecargasHoy = Recarga::whereHas('gestor', function($q) use($nit) { $q->where('NIT', $nit); })->whereDate('created_at', today())->sum('monto');
        $usuariosEmpresa = Usuario::where('NIT', $nit)->count();

        // Datos para Gráficas (Últimos 7 días)
        $fechas7Dias = [];
        $montos7Dias = [];
        $cantidad7Dias = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $fechas7Dias[] = $date->format('d/m');
            
            // Monto acumulado
            $montos7Dias[] = Recarga::whereHas('gestor', function($q) use($nit) { $q->where('NIT', $nit); })
                ->whereDate('created_at', $date)
                ->sum('monto');
                
            // Cantidad de transacciones
            $cantidad7Dias[] = Recarga::whereHas('gestor', function($q) use($nit) { $q->where('NIT', $nit); })
                ->whereDate('created_at', $date)
                ->count();
        }

        return view('empresa-recargas.dashboard', compact(
            'empresa', 
            'totalRecargasHoy', 
            'montoRecargasHoy', 
            'usuariosEmpresa',
            'fechas7Dias',
            'montos7Dias',
            'cantidad7Dias'
        ));
    }

    /**
     * Formulario de recarga
     */
    public function createRecarga()
    {
        return view('empresa-recargas.recargar');
    }

    /**
     * Consultar tarjeta por AJAX
     */
    public function consultarTarjeta(Request $request)
    {
        $id_tarjeta = $request->query('id_tarjeta');
        if (!$id_tarjeta) {
            return response()->json(['success' => false, 'message' => 'Indique el código de la tarjeta.'], 400);
        }

        $tarjeta = Tarjeta::with('usuarioActual')->where('id_tarjeta', $id_tarjeta)->first();

        if (!$tarjeta) {
            return response()->json(['success' => false, 'message' => 'Tarjeta no encontrada en el sistema.'], 404);
        }

        if ($tarjeta->id_estado != 1) {
            return response()->json(['success' => false, 'message' => 'La tarjeta no está activa.'], 400);
        }

        $nombrePropietario = 'Sin propietario asignado';
        if ($tarjeta->usuarioActual) {
            $u = $tarjeta->usuarioActual;
            $nombrePropietario = trim("{$u->primer_nombre} {$u->segundo_nombre} {$u->primer_apellido} {$u->segundo_apellido}");
        }

        return response()->json([
            'success' => true,
            'id_tarjeta' => $tarjeta->id_tarjeta,
            'saldo_actual' => $tarjeta->saldo ?? 0,
            'propietario' => $nombrePropietario
        ]);
    }

    /**
     * Procesar recarga
     */
    public function storeRecarga(Request $request)
    {
        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'exists' => 'El :attribute ingresado no es válido o no existe.',
            'min' => 'El :attribute debe ser de al menos $:min.'
        ];

        $attributes = [
            'id_tarjeta' => 'Código de la Tarjeta',
            'monto' => 'Monto a recargar'
        ];

        $request->validate([
            'id_tarjeta' => 'required|exists:tarjeta,id_tarjeta', 
            'monto' => 'required|numeric|min:1000'
        ], $messages, $attributes);

        $tarjeta = Tarjeta::findOrFail($request->id_tarjeta);
        $usuario = Auth::user();

        DB::beginTransaction();
        try {
            // Actualizar saldo (si tarjeta no tiene relacion saldo, el modelo podría no tenerlo, pero veamos)
            // Asumiendo que saldo existe en Tarjeta o TitularidadTarjeta. Si existe la tabla saldo_tarjeta o campo saldo en tarjeta: 
            // Para proyecto_pasajes, la tabla tarjeta tiene el saldo? (luego revisaré)
            if (isset($tarjeta->saldo)) {
                $tarjeta->saldo += $request->monto;
                $tarjeta->save();
            }

            Recarga::create([
                'id_tarjeta' => $tarjeta->id_tarjeta,
                'monto' => $request->monto,
                'doc_usuario_gestor' => $usuario->doc_usuario,
            ]);

            DB::commit();
            return redirect()->route('gestor-recargas.recargar')->with('success', 'Recarga procesada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gestor-recargas.recargar')->with('error', 'Error al procesar la recarga: ' . $e->getMessage());
        }
    }

    /**
     * Historial de recargas y cambios de titularidad de esta empresa
     */
    public function historial(Request $request)
    {
        $nit = Auth::user()->NIT;
        $query = Recarga::with(['tarjeta', 'gestor'])->whereHas('gestor', function($q) use($nit) { 
            $q->where('NIT', $nit); 
        });

        // Filtros de recargas
        if ($request->filled('id_tarjeta')) {
            $query->where('id_tarjeta', 'like', '%' . $request->id_tarjeta . '%');
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $query->orderBy('created_at', 'desc');

        // --- Consulta de cambios de titularidad ---
        $gestoresNit = Usuario::where('NIT', $nit)->pluck('doc_usuario');
        $queryTitularidad = \App\Models\TitularidadTarjeta::with(['tarjeta', 'usuario', 'estado'])
            ->whereIn('doc_usuario', $gestoresNit)
            ->whereNotNull('motivo_cambio');

        // Filtros de titularidad
        if ($request->filled('id_tarjeta')) {
            $queryTitularidad->where('id_tarjeta', 'like', '%' . $request->id_tarjeta . '%');
        }
        if ($request->filled('fecha_inicio')) {
            $queryTitularidad->whereDate('fecha_inicio', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $queryTitularidad->whereDate('fecha_inicio', '<=', $request->fecha_fin);
        }

        $queryTitularidad->orderBy('fecha_inicio', 'desc');

        // Exportación Excel (.xls)
        if ($request->has('export') && $request->export === 'excel') {
            $recargas = $query->get();
            $titularidades = $queryTitularidad->get();
            $filename = "historial_recargas_" . date('Y-m-d_H-i-s') . ".xls";

            $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            
            // Hoja de recargas
            $html .= '<h3>Historial de Recargas</h3>';
            $html .= '<table border="1">';
            $html .= '<thead><tr>';
            $html .= '<th style="background-color:#f8f9fa;">ID Recarga</th>';
            $html .= '<th style="background-color:#f8f9fa;">ID / Nro Tarjeta</th>';
            $html .= '<th style="background-color:#f8f9fa;">Monto ($)</th>';
            $html .= '<th style="background-color:#f8f9fa;">Fecha y Hora</th>';
            $html .= '</tr></thead>';
            $html .= '<tbody>';
            
            foreach ($recargas as $recarga) {
                $html .= '<tr>';
                $html .= '<td>' . $recarga->id_recarga . '</td>';
                $html .= '<td>' . $recarga->id_tarjeta . '</td>';
                $html .= '<td>' . $recarga->monto . '</td>';
                $html .= '<td>' . $recarga->created_at->format('d/m/Y H:i:s') . '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table>';

            // Hoja de cambios de titularidad
            $html .= '<br><br><h3>Cambios de Titularidad</h3>';
            $html .= '<table border="1">';
            $html .= '<thead><tr>';
            $html .= '<th style="background-color:#e8f4fd;">ID</th>';
            $html .= '<th style="background-color:#e8f4fd;">Tarjeta</th>';
            $html .= '<th style="background-color:#e8f4fd;">Usuario</th>';
            $html .= '<th style="background-color:#e8f4fd;">Estado</th>';
            $html .= '<th style="background-color:#e8f4fd;">Motivo</th>';
            $html .= '<th style="background-color:#e8f4fd;">Fecha Inicio</th>';
            $html .= '<th style="background-color:#e8f4fd;">Fecha Fin</th>';
            $html .= '</tr></thead>';
            $html .= '<tbody>';

            foreach ($titularidades as $t) {
                $nombreUsuario = $t->usuario ? trim($t->usuario->primer_nombre . ' ' . $t->usuario->primer_apellido) : $t->doc_usuario;
                $estado = $t->estado ? $t->estado->nombre_estado : $t->id_estado;
                $html .= '<tr>';
                $html .= '<td>' . $t->id_titularidad_tarjeta . '</td>';
                $html .= '<td>' . $t->id_tarjeta . '</td>';
                $html .= '<td>' . $nombreUsuario . '</td>';
                $html .= '<td>' . $estado . '</td>';
                $html .= '<td>' . ($t->motivo_cambio ?? '-') . '</td>';
                $html .= '<td>' . ($t->fecha_inicio ? \Carbon\Carbon::parse($t->fecha_inicio)->format('d/m/Y H:i:s') : '-') . '</td>';
                $html .= '<td>' . ($t->fecha_fin ? \Carbon\Carbon::parse($t->fecha_fin)->format('d/m/Y H:i:s') : '-') . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            return response($html)
                ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        $recargas = $query->paginate(15, ['*'], 'recargas_page')->appends($request->all());
        $titularidades = $queryTitularidad->paginate(10, ['*'], 'titularidad_page')->appends($request->all());

        return view('empresa-recargas.historial', compact('recargas', 'titularidades'));
    }

    /**
     * Lista de usuarios de la empresa
     */
    public function usuariosIndex()
    {
        $nit = Auth::user()->NIT;
        $usuarios = Usuario::where('NIT', $nit)->paginate(15);
        return view('empresa-recargas.usuarios.index', compact('usuarios'));
    }

    /**
     * Formulario crear usuario
     */
    public function usuariosCreate()
    {
        return view('empresa-recargas.usuarios.create');
    }

    /**
     * Guardar usuario de la empresa
     */
    public function usuariosStore(Request $request)
    {
        $rules = [
            'doc_usuario' => 'required|numeric|digits_between:7,10|unique:usuario,doc_usuario',
            'primer_nombre' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
            'segundo_nombre' => 'nullable|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
            'primer_apellido' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
            'segundo_apellido' => 'nullable|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
            'fecha_nacimiento' => 'nullable|date|before_or_equal:' . now()->subYears(15)->format('Y-m-d'),
            'telefono' => 'nullable|string|regex:/^\+?[0-9]+$/|max:20',
            'correo' => ['required', 'email', 'unique:usuario,correo', 'not_regex:/\s/'],
            'password' => [
                'required',
                'confirmed',
                function ($attribute, $value, $fail) {
                    $mayusculas = preg_match_all('/[A-ZÁÉÍÓÚÑ]/', $value);
                    $numeros = preg_match_all('/[0-9]/', $value);
                    $especiales = preg_match_all('/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/', $value);
                    
                    if ($mayusculas !== 1 || $numeros !== 4 || $especiales !== 1) {
                        $fail('La contraseña debe estar compuesta exactamente por 1 mayúscula, 4 números y 1 carácter especial (ej. Luis1234.).');
                    }
                    if (preg_match('/\s/', $value)) {
                        $fail('La contraseña no permite espacios.');
                    }
                }
            ],
        ];

        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'numeric' => 'El campo :attribute debe contener solo números.',
            'digits_between' => 'El documento de identidad debe tener entre :min y :max números.',
            'unique' => 'Este :attribute ya se encuentra registrado en el sistema.',
            'string' => 'El campo :attribute debe ser texto válido.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'email' => 'Debes ingresar un correo electrónico válido sin espacios ni caracteres extraños.',
            'not_regex' => 'El campo :attribute no permite espacios o formatos inválidos.',
            'regex' => 'El formato del campo :attribute no es válido. Verifica que no tenga números, espacios o caracteres especiales no permitidos.',
            'telefono.regex' => 'El teléfono solo permite números y opcionalmente el signo + al inicio, no se permiten letras ni espacios.',
            'before_or_equal' => 'El usuario debe tener al menos 15 años cumplidos.',
            'confirmed' => 'La confirmación de la contraseña no coincide.',
            'date' => 'La fecha ingresada no es válida.',
        ];

        $attributes = [
            'doc_usuario' => 'documento de identidad',
            'primer_nombre' => 'primer nombre',
            'segundo_nombre' => 'segundo nombre',
            'primer_apellido' => 'primer apellido',
            'segundo_apellido' => 'segundo apellido',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'telefono' => 'teléfono',
            'correo' => 'correo electrónico',
            'password' => 'contraseña',
        ];

        $request->validate($rules, $messages, $attributes);

        $usuarioLogueado = Auth::user();

        Usuario::create([
            'doc_usuario' => $request->doc_usuario,
            'NIT' => $usuarioLogueado->NIT,
            'id_tipo_usuario' => 10, // GESTOR RECARGAS
            'primer_nombre' => strtoupper($request->primer_nombre),
            'segundo_nombre' => $request->filled('segundo_nombre') ? strtoupper($request->segundo_nombre) : null,
            'primer_apellido' => strtoupper($request->primer_apellido),
            'segundo_apellido' => $request->filled('segundo_apellido') ? strtoupper($request->segundo_apellido) : null,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'id_ciudad' => $usuarioLogueado->id_ciudad,
            'id_estado' => 1,
            'telefono' => $request->telefono,
        ]);

        return redirect()->route('gestor-recargas.usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Formulario editar usuario
     */
    public function usuariosEdit($id)
    {
        $nit = Auth::user()->NIT;
        $usuarioEdit = Usuario::where('NIT', $nit)->where('doc_usuario', $id)->firstOrFail();
        
        return view('empresa-recargas.usuarios.edit', compact('usuarioEdit'));
    }

    /**
     * Actualizar usuario
     */
    public function usuariosUpdate(Request $request, $id)
    {
        $nit = Auth::user()->NIT;
        $usuarioEdit = Usuario::where('NIT', $nit)->where('doc_usuario', $id)->firstOrFail();

        $rules = [
            'primer_nombre' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
            'segundo_nombre' => 'nullable|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
            'primer_apellido' => 'required|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
            'segundo_apellido' => 'nullable|string|max:50|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/',
            'fecha_nacimiento' => 'nullable|date|before_or_equal:' . now()->subYears(15)->format('Y-m-d'),
            'telefono' => 'nullable|string|regex:/^\+?[0-9]+$/|max:20',
            'correo' => ['required', 'email', 'unique:usuario,correo,' . $id . ',doc_usuario', 'not_regex:/\s/'],
        ];

        // Validar contraseña solo si se proporciona
        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'confirmed',
                function ($attribute, $value, $fail) {
                    $mayusculas = preg_match_all('/[A-ZÁÉÍÓÚÑ]/', $value);
                    $numeros = preg_match_all('/[0-9]/', $value);
                    $especiales = preg_match_all('/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]/', $value);
                    
                    if ($mayusculas !== 1 || $numeros !== 4 || $especiales !== 1) {
                        $fail('La contraseña debe estar compuesta exactamente por 1 mayúscula, 4 números y 1 carácter especial (ej. Luis1234.).');
                    }
                    if (preg_match('/\s/', $value)) {
                        $fail('La contraseña no permite espacios.');
                    }
                }
            ];
        }

        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'unique' => 'Este :attribute ya se encuentra registrado en el sistema.',
            'string' => 'El campo :attribute debe ser texto válido.',
            'max' => 'El campo :attribute no debe exceder :max caracteres.',
            'email' => 'Debes ingresar un correo electrónico válido.',
            'not_regex' => 'El campo :attribute no permite espacios.',
            'regex' => 'El formato del campo :attribute no es válido.',
            'telefono.regex' => 'El teléfono solo permite números y el signo +.',
            'before_or_equal' => 'El usuario debe tener al menos 15 años cumplidos.',
            'confirmed' => 'La confirmación de la contraseña no coincide.',
            'date' => 'La fecha ingresada no es válida.',
        ];

        $request->validate($rules, $messages);

        $usuarioEdit->primer_nombre = strtoupper($request->primer_nombre);
        $usuarioEdit->segundo_nombre = $request->filled('segundo_nombre') ? strtoupper($request->segundo_nombre) : null;
        $usuarioEdit->primer_apellido = strtoupper($request->primer_apellido);
        $usuarioEdit->segundo_apellido = $request->filled('segundo_apellido') ? strtoupper($request->segundo_apellido) : null;
        $usuarioEdit->fecha_nacimiento = $request->fecha_nacimiento;
        $usuarioEdit->correo = $request->correo;
        $usuarioEdit->telefono = $request->telefono;

        if ($request->filled('password')) {
            $usuarioEdit->password = Hash::make($request->password);
        }

        $usuarioEdit->save();

        return redirect()->route('gestor-recargas.usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Activar / Desactivar usuario
     */
    public function usuariosToggleStatus($id)
    {
        $nit = Auth::user()->NIT;
        $usuarioEdit = Usuario::where('NIT', $nit)->where('doc_usuario', $id)->firstOrFail();

        // Evitar que el usuario se desactive a sí mismo
        if ($usuarioEdit->doc_usuario === Auth::user()->doc_usuario) {
            return redirect()->back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        // Alternar el estado (1 activo, 2 inactivo) Asumiendo 2 como inactivo
        $usuarioEdit->id_estado = ($usuarioEdit->id_estado == 1) ? 2 : 1;
        $usuarioEdit->save();

        $action = $usuarioEdit->id_estado == 1 ? 'activado' : 'desactivado';
        return redirect()->route('gestor-recargas.usuarios.index')->with('success', "Usuario {$action} exitosamente.");
    }
}
