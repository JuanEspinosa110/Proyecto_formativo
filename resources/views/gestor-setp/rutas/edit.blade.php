@extends('gestor-setp.layouts.app')

@section('title', 'Editar Ruta #' . $ruta->codigo_ruta)

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- ── Breadcrumb ──────────────────────────────────────────── --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.83rem">
            <li class="breadcrumb-item">
                <a href="{{ route('gestor-setp.rutas.index') }}" style="color:var(--acc)">Rutas</a>
            </li>
            <li class="breadcrumb-item active">Editar Ruta #{{ $ruta->codigo_ruta }}</li>
        </ol>
    </nav>

    <div class="rt-form-wrap">

        {{-- ── Info actual de la ruta ─────────────────────────── --}}
        <div class="d-flex align-items-center gap-3 mb-4 p-3"
             style="background:var(--acc-l);border:1px solid var(--acc);border-radius:var(--r-md)">
            <div style="width:46px;height:46px;background:linear-gradient(135deg,var(--acc),#2A9E6A);
                        border-radius:var(--r);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span class="material-symbols-rounded" style="color:#fff;font-size:1.4rem;font-variation-settings:var(--ms-on)">alt_route</span>
            </div>
            <div class="flex-grow-1">
                <div style="font-family:var(--ff-d);font-weight:700;font-size:1rem;color:var(--text)">
                    Ruta #{{ $ruta->codigo_ruta }}
                </div>
                <div style="font-size:.82rem;color:var(--text-2)">
                    <span class="material-symbols-rounded" style="font-size:.9rem;vertical-align:middle">location_on</span>
                    {{ $ruta->barrioOrigen->nombre ?? '—' }}
                    <span class="material-symbols-rounded" style="font-size:.9rem;vertical-align:middle;color:var(--acc)">arrow_forward</span>
                    {{ $ruta->barrioDestino->nombre ?? '—' }}
                    &nbsp;·&nbsp;
                    <span class="material-symbols-rounded" style="font-size:.9rem;vertical-align:middle">location_city</span>
                    {{ auth()->user()->ciudad->nombre_city ?? 'tu ciudad' }}
                </div>
            </div>
            {{-- Estado actual --}}
            @if($ruta->id_estado == 1)
                <span class="gs-badge gs-badge-active">
                    <span class="material-symbols-rounded" style="font-size:.8rem">circle</span> Activa
                </span>
            @else
                <span class="gs-badge gs-badge-inactive">
                    <span class="material-symbols-rounded" style="font-size:.8rem">cancel</span> Inactiva
                </span>
            @endif
        </div>

        {{-- ── Formulario ──────────────────────────────────────── --}}
        <div class="rt-form-card">
            <div class="rt-form-header">
                <div class="icon-wrap">
                    <span class="material-symbols-rounded">edit_road</span>
                </div>
                <div>
                    <h2>Editar Ruta</h2>
                    <p>Modifica el código, el recorrido o el estado de la ruta.</p>
                </div>
            </div>

            <form method="POST"
                  action="{{ route('gestor-setp.rutas.update', $ruta->id_ruta) }}"
                  novalidate>
                @csrf
                @method('PUT')

                <div class="rt-form-body">

                    {{-- ── Información básica ───────────────────── --}}
                    <p class="rt-section">
                        <span class="material-symbols-rounded">info</span>
                        Información de la ruta
                    </p>

                    <div class="row g-3 mb-4">

                        {{-- ID de la ruta (solo lectura) --}}
                        <div class="col-md-4">
                            <label class="form-label">ID de ruta</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $ruta->id_ruta }}"
                                   disabled
                                   style="background:var(--p-xlight);color:var(--text-2);cursor:not-allowed">
                            <p class="form-hint">Identificador único, no editable.</p>
                        </div>

                        {{-- Código de ruta --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Código de ruta <span class="req">*</span>
                            </label>
                            <input type="number"
                                   name="codigo_ruta"
                                   class="form-control @error('codigo_ruta') is-invalid @enderror"
                                   value="{{ old('codigo_ruta', $ruta->codigo_ruta) }}"
                                   placeholder="Ej: 23"
                                   min="1"
                                   max="99"
                                   required>
                            @error('codigo_ruta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="form-hint">Número entre 1 y 99.</p>
                        </div>

                        {{-- Estado --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Estado <span class="req">*</span>
                            </label>
                            <select name="id_estado"
                                    class="form-select @error('id_estado') is-invalid @enderror"
                                    required>
                                <option value="1" {{ old('id_estado', $ruta->id_estado) == 1 ? 'selected' : '' }}>
                                    Activa
                                </option>
                                <option value="2" {{ old('id_estado', $ruta->id_estado) == 2 ? 'selected' : '' }}>
                                    Inactiva
                                </option>
                            </select>
                            @error('id_estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- Ciudad oculta --}}
                    <input type="hidden" name="id_ciudad" value="{{ auth()->user()->id_ciudad }}">

                    {{-- ── Recorrido ────────────────────────────── --}}
                    <p class="rt-section">
                        <span class="material-symbols-rounded">route</span>
                        Recorrido
                    </p>

                    <div class="d-flex align-items-center gap-2 mb-3 p-2"
                         style="background:var(--acc-l);border:1px solid var(--acc);
                                border-radius:var(--r);font-size:.85rem;color:var(--acc)">
                        <span class="material-symbols-rounded" style="font-size:1rem">info</span>
                        Los barrios corresponden a
                        <strong>{{ auth()->user()->ciudad->nombre_city ?? 'tu ciudad' }}</strong>.
                        Para cambiar de ciudad contacta al SuperAdmin.
                    </div>

                    <div class="row g-3">

                        {{-- Barrio origen --}}
                        <div class="col-md-5">
                            <label class="form-label">
                                Barrio de origen <span class="req">*</span>
                            </label>
                            <select name="id_barrio_origen"
                                    class="form-select @error('id_barrio_origen') is-invalid @enderror"
                                    id="selectOrigen"
                                    required>
                                <option value="" disabled>— Seleccione barrio —</option>
                                @foreach($barrios as $barrio)
                                <option value="{{ $barrio->id_barrio }}"
                                        {{ old('id_barrio_origen', $ruta->id_barrio_origen) == $barrio->id_barrio ? 'selected' : '' }}>
                                    {{ $barrio->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_barrio_origen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Flecha visual --}}
                        <div class="col-md-2 d-flex align-items-center justify-content-center pt-3">
                            <span class="material-symbols-rounded"
                                  style="font-size:1.6rem;color:var(--acc)">arrow_forward</span>
                        </div>

                        {{-- Barrio destino --}}
                        <div class="col-md-5">
                            <label class="form-label">
                                Barrio de destino <span class="req">*</span>
                            </label>
                            <select name="id_barrio_destino"
                                    class="form-select @error('id_barrio_destino') is-invalid @enderror"
                                    id="selectDestino"
                                    required>
                                <option value="" disabled>— Seleccione barrio —</option>
                                @foreach($barrios as $barrio)
                                <option value="{{ $barrio->id_barrio }}"
                                        {{ old('id_barrio_destino', $ruta->id_barrio_destino) == $barrio->id_barrio ? 'selected' : '' }}>
                                    {{ $barrio->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_barrio_destino')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Error barrios iguales --}}
                        @if($errors->has('id_barrio_destino') && str_contains($errors->first('id_barrio_destino'), 'diferente'))
                        <div class="col-12">
                            <div class="alert d-flex align-items-center gap-2 py-2"
                                 style="background:var(--err-bg);border:1px solid var(--err);
                                        border-radius:var(--r);font-size:.84rem;color:var(--err)">
                                <span class="material-symbols-rounded" style="font-size:1rem">warning</span>
                                El barrio de destino debe ser diferente al de origen.
                            </div>
                        </div>
                        @endif

                    </div>

                    {{-- ── Empresas asignadas (solo lectura) ──────── --}}
                    @if($ruta->asignaciones && $ruta->asignaciones->count())
                    <div class="mt-4">
                        <p class="rt-section">
                            <span class="material-symbols-rounded">corporate_fare</span>
                            Empresas asignadas actualmente
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($ruta->asignaciones as $asig)
                                @if($asig->id_estado == 1)
                                <div style="display:inline-flex;align-items:center;gap:.4rem;
                                            background:var(--acc-l);border:1px solid var(--acc);
                                            border-radius:var(--r-xl);padding:.25rem .75rem;font-size:.82rem">
                                    <span class="material-symbols-rounded" style="font-size:.9rem;color:var(--acc)">directions_bus</span>
                                    <span style="font-weight:600;color:var(--acc)">
                                        {{ $asig->empresa->nombre_empresa ?? $asig->Nit }}
                                    </span>
                                    @if($asig->fecha_inicio)
                                    <span style="color:var(--text-2);font-size:.75rem">
                                        desde {{ \Carbon\Carbon::parse($asig->fecha_inicio)->format('d/m/Y') }}
                                    </span>
                                    @endif
                                </div>
                                @endif
                            @endforeach
                        </div>
                        <p class="form-hint mt-1">
                            Para gestionar las empresas usa la opción
                            <strong>Asignar empresa</strong> desde el listado de rutas.
                        </p>
                    </div>
                    @endif

                </div>{{-- /rt-form-body --}}

                {{-- ── Footer ───────────────────────────────────── --}}
                <div class="rt-form-footer">
                    <a href="{{ route('gestor-setp.rutas.index') }}"
                       class="btn btn-outline-secondary"
                       style="border-radius:var(--r-sm)">
                        Cancelar
                    </a>
                    <button type="submit" class="rt-btn rt-btn-primary">
                        <span class="material-symbols-rounded" style="font-size:1.1rem">save</span>
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>{{-- /rt-form-card --}}

    </div>{{-- /rt-form-wrap --}}

</div>
@endsection

@push('scripts')
<script>
// Evitar seleccionar el mismo barrio en origen y destino
(function () {
    const origen  = document.getElementById('selectOrigen');
    const destino = document.getElementById('selectDestino');

    function validarBarrios() {
        if (origen.value && destino.value && origen.value === destino.value) {
            destino.setCustomValidity('El barrio de destino debe ser diferente al de origen.');
            destino.classList.add('is-invalid');
        } else {
            destino.setCustomValidity('');
            destino.classList.remove('is-invalid');
        }
    }

    origen.addEventListener('change', validarBarrios);
    destino.addEventListener('change', validarBarrios);
})();
</script>
@endpush