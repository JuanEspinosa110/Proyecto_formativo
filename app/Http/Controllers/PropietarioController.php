<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Viaje;
use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Models\Estado;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PropietarioController extends Controller
{
    /**
     * Muestra la vista principal del propietario con toda su información.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Validar que el usuario sea propietario
        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Información del bus (Filtrado por doc_propietario)
        // Se asocia el docente del usuario autenticado con doc_propietario del bus
        $bus = Bus::with('estado')->where('doc_propietario', $user->doc_usuario)->first();

        // 2. Asignaciones del bus (Solo si tiene un bus asociado)
        $queryAsignaciones = Viaje::query()->with(['ruta', 'conductor', 'estado']);
        
        if ($bus) {
            $queryAsignaciones->where('placa', $bus->placa);
            
            // Filtros en asignaciones
            if ($request->filled('fecha')) {
                $queryAsignaciones->whereDate('fecha', $request->fecha);
            }
            if ($request->filled('conductor')) {
                $queryAsignaciones->whereHas('conductor', function($q) use ($request) {
                    $q->where('primer_nombre', 'like', '%' . $request->conductor . '%')
                      ->orWhere('primer_apellido', 'like', '%' . $request->conductor . '%');
                });
            }
            if ($request->filled('estado')) {
                $queryAsignaciones->where('id_estado', $request->estado);
            }
        } else {
            // Si no tiene bus, forzamos resultado vacío
            $queryAsignaciones->whereRaw('1 = 0');
        }

        $asignaciones = $queryAsignaciones->orderBy('fecha', 'desc')->paginate(10);
        
        // Conteos para tarjetas
        $conteoAsignaciones = $bus ? Viaje::where('placa', $bus->placa)->count() : 0;
        $conteoDocumentos = $bus ? Documento::where('placa', $bus->placa)->count() : 0;

        // 3. Documentación del bus
        $documentos = $bus ? Documento::where('placa', $bus->placa)->with(['tipoDocumento', 'estado'])->get() : collect();
        
        // Datos para formularios
        $tiposDocumento = TipoDocumento::where('id_estado', 1)->get();
        $estados = Estado::all();

        return view('propietario.dashboard', compact('bus', 'asignaciones', 'documentos', 'tiposDocumento', 'estados', 'conteoAsignaciones', 'conteoDocumentos'));
    }

    /**
     * Sube un nuevo documento para el bus del propietario.
     */
    public function subirDocumento(Request $request)
    {
        $user = Auth::user();
        $bus = Bus::where('doc_propietario', $user->doc_usuario)->first();

        if (!$bus) {
            return redirect()->back()->with('error', 'No tienes un vehículo asociado para subir documentos.');
        }

        $request->validate([
            'nombre' => 'required|string|max:150',
            'id_tipo_documento' => 'required|exists:tipo_documento,id_tipo_documento',
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'fecha_expedicion' => 'required|date|before_or_equal:today',
            'fecha_vencimiento' => 'required|date|after:fecha_expedicion',
        ]);

        try {
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                
                // Generar nombre seguro
                $timestamp = time();
                $nombreOriginal = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
                $nombreArchivo = $timestamp . '_' . substr($nombreOriginal, 0, 50) . '.' . $archivo->getClientOriginalExtension();
                
                // Guardar en storage
                $ruta = $archivo->storeAs(
                    'documentos/' . ($bus->NIT ?? 'propietarios'),
                    $nombreArchivo,
                    'public'
                );

                if (!$ruta) {
                    return redirect()->back()->with('error', 'Error al guardar el archivo en el servidor.');
                }

                // Crear el registro en la base de datos
                Documento::create([
                    'id_documento' => (Documento::max('id_documento') ?? 0) + 1,
                    'nombre' => $request->nombre,
                    'archivo' => $ruta,
                    'fecha_expedicion' => $request->fecha_expedicion,
                    'fecha_vencimiento' => $request->fecha_vencimiento,
                    'id_tipo_documento' => $request->id_tipo_documento,
                    'placa' => $bus->placa,
                    'NIT' => $bus->NIT,
                    'id_estado' => 1, // Activo
                ]);

                return redirect()->back()->with('success', 'Documento subido exitosamente.');
            }
        } catch (\Exception $e) {
            Log::error('Error al subir documento propietario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno al subir el documento.');
        }

        return redirect()->back()->with('error', 'No se pudo procesar el archivo.');
    }

    /**
     * Actualiza un documento existente.
     */
    public function actualizarDocumento(Request $request, $id)
    {
        $user = Auth::user();
        $bus = Bus::where('doc_propietario', $user->doc_usuario)->first();

        if (!$bus) {
            return redirect()->back()->with('error', 'No tienes un vehículo asociado.');
        }

        $documento = Documento::where('id_documento', $id)
            ->where('placa', $bus->placa)
            ->firstOrFail();

        $request->validate([
            'nombre' => 'required|string|max:150',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'fecha_expedicion' => 'required|date|before_or_equal:today',
            'fecha_vencimiento' => 'required|date|after:fecha_expedicion',
        ]);

        try {
            $data = [
                'nombre' => $request->nombre,
                'fecha_expedicion' => $request->fecha_expedicion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
            ];

            if ($request->hasFile('archivo')) {
                // Eliminar archivo anterior si existe
                if ($documento->archivo && Storage::disk('public')->exists($documento->archivo)) {
                    Storage::disk('public')->delete($documento->archivo);
                }

                $archivo = $request->file('archivo');
                $timestamp = time();
                $nombreOriginal = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
                $nombreArchivo = $timestamp . '_' . substr($nombreOriginal, 0, 50) . '.' . $archivo->getClientOriginalExtension();
                
                $ruta = $archivo->storeAs(
                    'documentos/' . ($bus->NIT ?? 'propietarios'),
                    $nombreArchivo,
                    'public'
                );
                
                $data['archivo'] = $ruta;
            }

            $documento->update($data);

            return redirect()->back()->with('success', 'Documento actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar documento propietario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno al actualizar el documento.');
        }
    }
}
