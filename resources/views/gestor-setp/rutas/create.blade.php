@extends('gestor-setp.layouts.app')

@section('title', 'Nueva Ruta')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- ── Breadcrumb ──────────────────────────────────────────── --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.83rem">
            <li class="breadcrumb-item">
                <a href="{{ route('gestor-setp.rutas.index') }}" style="color:var(--acc)">Rutas</a>
            </li>
            <li class="breadcrumb-item active">Nueva ruta</li>
        </ol>
    </nav>

    <div class="rt-form-wrap">
        <div class="rt-form-card">

            {{-- ── Header ──────────────────────────────────────── --}}
            <div class="rt-form-header">
                <div class="icon-wrap">
                    <span class="material-symbols-rounded">add_road</span>
                </div>
                <div>
                    <h2>Nueva Ruta</h2>
                    <p>Complete la información del recorrido para crear la ruta en tu ciudad.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('gestor-setp.rutas.store') }}" novalidate>
                @csrf
                <div class="rt-form-body">

                    {{-- ── Información básica ───────────────────── --}}
                    <p class="rt-section">
                        <span class="material-symbols-rounded">info</span>
                        Información de la ruta
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">
                                Código de ruta <span class="req">*</span>
                            </label>
                            <input type="number"
                                   name="codigo_ruta"
                                   class="form-control @error('codigo_ruta') is-invalid @enderror"
                                   value="{{ old('codigo_ruta') }}"
                                   placeholder="Ej: 23"
                                   min="1"
                                   max="99"
                                   required>
                            @error('codigo_ruta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="form-hint">Número entre 1 y 99 que identifica la ruta.</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Estado <span class="req">*</span>
                            </label>
                            <select name="id_estado"
                                    class="form-select @error('id_estado') is-invalid @enderror"
                                    required>
                                <option value="1" {{ old('id_estado', '1') == '1' ? 'selected' : '' }}>Activa</option>
                                <option value="2" {{ old('id_estado') == '2' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                            @error('id_estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="form-hint">Las rutas se crean activas por defecto.</p>
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
                        Los barrios disponibles corresponden a la ciudad asignada a tu cuenta:
                        <strong>{{ auth()->user()->ciudad->nombre_city ?? 'tu ciudad' }}</strong>.
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
                                <option value="" disabled {{ old('id_barrio_origen') ? '' : 'selected' }}>
                                    — Seleccione barrio —
                                </option>
                                @foreach($barrios as $barrio)
                                <option value="{{ $barrio->id_barrio }}"
                                        {{ old('id_barrio_origen') == $barrio->id_barrio ? 'selected' : '' }}>
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
                                <option value="" disabled {{ old('id_barrio_destino') ? '' : 'selected' }}>
                                    — Seleccione barrio —
                                </option>
                                @foreach($barrios as $barrio)
                                <option value="{{ $barrio->id_barrio }}"
                                        {{ old('id_barrio_destino') == $barrio->id_barrio ? 'selected' : '' }}>
                                    {{ $barrio->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_barrio_destino')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alerta barrios iguales (server-side) --}}
                        @if($errors->has('id_barrio_destino') && str_contains($errors->first('id_barrio_destino'), 'diferente'))
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 p-2"
                                 style="background:var(--err-bg);border:1px solid var(--err);
                                        border-radius:var(--r);font-size:.84rem;color:var(--err)">
                                <span class="material-symbols-rounded" style="font-size:1rem">warning</span>
                                El barrio de destino debe ser diferente al de origen.
                            </div>
                        </div>
                        @endif

                    </div>

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
                        Crear ruta
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Validación client-side: origen ≠ destino
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