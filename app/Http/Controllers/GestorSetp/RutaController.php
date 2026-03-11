<?php

namespace App\Http\Controllers\GestorSetp;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Barrio;
use App\Models\Empresa;
use App\Models\Asignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RutaController extends Controller
{
    // id_tipo_asignacion = 3 → "BUS A RUTA" (asignación de empresa a ruta)
    private const TIPO_ASIGNACION_RUTA = 3;
    // id_tipo_empresa = 1 → Transporte Urbano
    private const TIPO_EMPRESA_TRANSPORTE = 1;

    // ── index ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $ciudad = auth()->user()->id_ciudad;

        $query = Ruta::with(['barrioOrigen', 'barrioDestino', 'ciudad', 'asignaciones.empresa'])
            ->where('id_ciudad', $ciudad);

        if ($request->filled('codigo')) {
            $query->where('codigo_ruta', $request->codigo);
        }

        if ($request->filled('estado')) {
            $query->where('id_estado', $request->estado);
        }

        $rutas = $query->orderByDesc('id_ruta')->paginate(9);

        // Empresas de transporte de la ciudad (para el modal de asignación)
        $empresasTransporte = Empresa::where('id_ciudad', $ciudad)
            ->where('id_tipo_empresa', self::TIPO_EMPRESA_TRANSPORTE)
            ->where('id_estado', 1)
            ->get();

        return view('gestor-setp.rutas.index', compact('rutas', 'empresasTransporte'));
    }

    // ── create ────────────────────────────────────────────────────
    public function create()
    {
        $barrios = Barrio::where('id_ciudad', auth()->user()->id_ciudad)
            ->orderBy('nombre')
            ->get();

        return view('gestor-setp.rutas.create', compact('barrios'));
    }

    // ── store ─────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_ruta'       => 'required|integer|unique:ruta,codigo_ruta|min:1',
            'id_barrio_origen'  => 'required|exists:barrio,id_barrio',
            'id_barrio_destino' => 'required|exists:barrio,id_barrio',
            'id_estado'         => 'required|in:1,2',
        ], [
            'codigo_ruta.required'       => 'El código de ruta es obligatorio.',
            'codigo_ruta.integer'        => 'El código debe ser un número entero.',
            'codigo_ruta.unique'         => 'Este código de ruta ya está registrado.',
            'id_barrio_origen.required'  => 'Debe seleccionar el barrio de origen.',
            'id_barrio_origen.exists'    => 'El barrio de origen no existe.',
            'id_barrio_destino.required' => 'Debe seleccionar el barrio de destino.',
            'id_barrio_destino.exists'   => 'El barrio de destino no existe.',
        ]);

        if ($data['id_barrio_origen'] === $data['id_barrio_destino']) {
            return back()
                ->withErrors(['id_barrio_destino' => 'El barrio de destino debe ser diferente al de origen.'])
                ->withInput();
        }
        // Generamos el ID aleatorio de 6 dígitos único
        $nuevoId = $this->generarIdRutaAleatorio();

        Ruta::create([
            'id_ruta'        => $nuevoId,
            'codigo_ruta'    => $data['codigo_ruta'],
            'id_barrio_origen' => $data['id_barrio_origen'],
            'id_barrio_destino' => $data['id_barrio_destino'],
            'id_ciudad'      => auth()->user()->id_ciudad,
            'id_estado'      => 1, // Activa por defecto
        ]);

        return redirect()->route('gestor-setp.rutas.index')
            ->with('success', 'Ruta creada correctamente.');
    }

    // ── edit ──────────────────────────────────────────────────────
    public function edit($id)
    {
        $ciudad = auth()->user()->id_ciudad;

        // Buscamos la ruta asegurándonos que pertenezca a la ciudad del gestor
        $ruta = Ruta::where('id_ruta', $id)
            ->where('id_ciudad', $ciudad)
            ->firstOrFail();

        // Necesitamos los barrios para los select del formulario
        $barrios = Barrio::where('id_ciudad', $ciudad)
            ->orderBy('nombre')
            ->get();

        return view('gestor-setp.rutas.edit', compact('ruta', 'barrios'));
    }

    // ── update ────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $ruta = Ruta::where('id_ruta', $id)
            ->where('id_ciudad', auth()->user()->id_ciudad)
            ->firstOrFail();

        $data = $request->validate([
            'codigo_ruta' => [
                'required',
                'regex:/^[1-9][0-9]*$/',
                'integer',
                'min:1',
                'max:99',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = DB::table('ruta')
                        ->where('codigo_ruta', $value)
                        ->when($id, function ($q) use ($id) {
                            return $q->where('id_ruta', '!=', $id);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('Este código de ruta ya está registrado.');
                    }
                }
            ],
            'id_barrio_origen'  => 'required|exists:barrio,id_barrio',
            'id_barrio_destino' => 'required|exists:barrio,id_barrio',
            'id_estado'         => 'required|in:1,2',
        ], [
            'codigo_ruta.required'       => 'El código de ruta es obligatorio.',
            'id_barrio_origen.required'  => 'Debe seleccionar el barrio de origen.',
            'id_barrio_destino.required' => 'Debe seleccionar el barrio de destino.',
        ]);

        if ($data['id_barrio_origen'] === $data['id_barrio_destino']) {
            return back()
                ->withErrors(['id_barrio_destino' => 'El barrio de destino debe ser diferente al de origen.'])
                ->withInput();
        }
        $ruta = Ruta::where('id_ruta', $id)
            ->where('id_ciudad', auth()->user()->id_ciudad)
            ->firstOrFail();

        $ruta->update([
            'codigo_ruta'    => $request->codigo_ruta,
            'id_barrio_orig' => $request->id_barrio_orig,
            'id_barrio_dest' => $request->id_barrio_dest,
            'id_estado'      => $request->id_estado,
        ]);

        return redirect()->route('gestor-setp.rutas.index')
            ->with('success', 'Ruta actualizada correctamente.');
    }

    // ── toggleEstado ──────────────────────────────────────────────
    public function toggleEstado($id)
    {
        $ruta = Ruta::where('id_ruta', $id)
            ->where('id_ciudad', auth()->user()->id_ciudad)
            ->firstOrFail();

        $ruta->update(['id_estado' => $ruta->id_estado == 1 ? 2 : 1]);

        $accion = $ruta->id_estado == 1 ? 'activada' : 'inactivada';
        return back()->with('success', "Ruta #{$ruta->codigo_ruta} {$accion} correctamente.");
    }

    // ── formAsignar ───────────────────────────────────────────────
    public function formAsignar($id)
    {
        $ruta = Ruta::with(['barrioOrigen', 'barrioDestino', 'asignaciones.empresa'])
            ->where('id_ruta', $id)
            ->where('id_ciudad', auth()->user()->id_ciudad)
            ->firstOrFail();

        $empresasTransporte = Empresa::where('id_ciudad', auth()->user()->id_ciudad)
            ->where('id_tipo_empresa', self::TIPO_EMPRESA_TRANSPORTE)
            ->where('id_estado', 1)
            ->get();

        return view('gestor-setp.rutas.asignar', compact('ruta', 'empresasTransporte'));
    }

    // ── asignar ───────────────────────────────────────────────────
    public function asignar(Request $request, $id)
    {
        $ruta = Ruta::where('id_ruta', $id)
            ->where('id_ciudad', auth()->user()->id_ciudad)
            ->firstOrFail();

        $data = $request->validate([
            'Nit'           => 'required|exists:empresa,NIT',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'nullable|date|after:fecha_inicio',
        ], [
            'Nit.required'          => 'Debe seleccionar una empresa.',
            'Nit.exists'            => 'La empresa seleccionada no existe.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.after'       => 'La fecha de fin debe ser posterior a la de inicio.',
        ]);

        // Verificar que la empresa no esté ya asignada a esta ruta con estado activo
        $yaAsignada = Asignacion::where('id_ruta', $ruta->id_ruta)
            ->where('Nit', $data['Nit'])
            ->where('id_estado', 1)
            ->exists();

        if ($yaAsignada) {
            return back()->with('error', 'Esta empresa ya tiene una asignación activa en esta ruta.');
        }

        Asignacion::create([
            'id_tipo_asignacion' => self::TIPO_ASIGNACION_RUTA,
            'id_ruta'            => $ruta->id_ruta,
            'Nit'                => $data['Nit'],
            'fecha_inicio'       => $data['fecha_inicio'],
            'fecha_fin'          => $data['fecha_fin'] ?? null,
            'id_estado'          => 1,
            'doc_usuario'        => auth()->id(),
            'placa'              => null,
        ]);

        return redirect()->route('gestor-setp.rutas.index')
            ->with('success', 'Empresa asignada a la ruta correctamente.');
    }

    // ── desasignar ────────────────────────────────────────────────
    public function desasignar($idAsignacion)
    {
        $asignacion = Asignacion::where('id_asignacion', $idAsignacion)
            ->where('id_tipo_asignacion', self::TIPO_ASIGNACION_RUTA)
            ->firstOrFail();

        // Verificar que la ruta pertenece a la ciudad del gestor
        $ruta = Ruta::where('id_ruta', $asignacion->id_ruta)
            ->where('id_ciudad', auth()->user()->id_ciudad)
            ->firstOrFail();

        $asignacion->update(['id_estado' => 2]);

        return back()->with('success', 'Empresa desasignada de la ruta correctamente.');
    }

    private function generarIdRutaAleatorio()
    {
        do {
            $codigo = mt_rand(100000, 999999); // Genera un número de 6 dígitos
        } while (Ruta::where('id_ruta', $codigo)->exists());

        return $codigo;
    }
}
