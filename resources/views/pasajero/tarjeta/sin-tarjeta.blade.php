@extends('pasajero.layouts.app')

@section('title', 'Activa tu tarjeta SIGU')

@section('content')
<div class="sin-tarjeta-wrap">
    <div class="sin-tarjeta-box">

        <div class="sin-tarjeta-icon">
            <span class="material-symbols-rounded">credit_card</span>
        </div>

        <h2>¡Bienvenido a SIGU!</h2>
        <p>Para usar el sistema de transporte necesitas una tarjeta SIGU.
           ¿Ya tienes una o quieres solicitar la tuya?</p>

        @if(session('info'))
        <div class="pas-alert info mb-3">
            <span class="material-symbols-rounded" style="font-size:1.1rem">info</span>
            {{ session('info') }}
        </div>
        @endif

        @if(session('success'))
        <div class="pas-alert ok mb-3">
            <span class="material-symbols-rounded" style="font-size:1.1rem">check_circle</span>
            {{ session('success') }}
        </div>
        @endif

        {{-- ── Opciones ──────────────────────────────────────── --}}
        <div class="sin-tarjeta-opciones">

            {{-- Registrar tarjeta existente --}}
            <div class="sin-tarjeta-opcion" id="btnRegistrar"
                 onclick="mostrarPanel('panelRegistrar')">
                <div class="opt-icon registrar">
                    <span class="material-symbols-rounded">qr_code_scanner</span>
                </div>
                <h4>Ya tengo tarjeta</h4>
                <p>Registra el código de tu tarjeta física SIGU</p>
            </div>

            {{-- Comprar tarjeta nueva --}}
            <div class="sin-tarjeta-opcion" id="btnComprar"
                 onclick="mostrarPanel('panelComprar')">
                <div class="opt-icon comprar">
                    <span class="material-symbols-rounded">add_card</span>
                </div>
                <h4>Quiero una tarjeta</h4>
                <p>Solicita tu tarjeta en un punto autorizado</p>
            </div>

        </div>

        {{-- ── Panel: Registrar código ───────────────────────── --}}
        <div class="sin-tarjeta-panel" id="panelRegistrar" style="display:none">
            <h5>
                <span class="material-symbols-rounded" style="color:var(--ok)">qr_code_scanner</span>
                Registrar código de tarjeta
            </h5>

            @error('codigo_tarjeta')
            <div class="pas-alert warn mb-3">
                <span class="material-symbols-rounded" style="font-size:1.1rem">warning</span>
                {{ $message }}
            </div>
            @enderror

            <form method="POST" action="{{ route('pasajero.tarjeta.registrar') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.84rem">
                        Código de tarjeta <span style="color:var(--err)">*</span>
                    </label>
                    <input type="text"
                           name="codigo_tarjeta"
                           class="form-control @error('codigo_tarjeta') is-invalid @enderror"
                           placeholder="Ej: SIGU-2025-00123"
                           value="{{ old('codigo_tarjeta') }}"
                           style="font-family:monospace;letter-spacing:.04em"
                           required autofocus>
                    @error('codigo_tarjeta')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <p style="font-size:.76rem;color:var(--text-2);margin-top:.3rem">
                        El código está impreso en el reverso de tu tarjeta física.
                    </p>
                </div>
                <button type="submit" class="pas-btn pas-btn-primary w-100">
                    <span class="material-symbols-rounded" style="font-size:1rem">link</span>
                    Vincular tarjeta
                </button>
            </form>
        </div>

        {{-- ── Panel: Solicitar tarjeta ──────────────────────── --}}
        <div class="sin-tarjeta-panel" id="panelComprar" style="display:none">
            <h5>
                <span class="material-symbols-rounded" style="color:var(--pas)">add_card</span>
                Solicitar tarjeta SIGU
            </h5>
            <p style="font-size:.83rem;color:var(--text-2);margin-bottom:1rem">
                Puedes obtener tu tarjeta en cualquier punto de atención autorizado.
                Indica en cuál deseas recogerla y recibirás instrucciones.
            </p>

            <form method="POST" action="{{ route('pasajero.tarjeta.comprar') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.84rem">
                        Punto de recogida <span style="color:var(--err)">*</span>
                    </label>
                    <input type="text"
                           name="punto_compra"
                           class="form-control @error('punto_compra') is-invalid @enderror"
                           placeholder="Nombre o dirección del punto"
                           value="{{ old('punto_compra') }}"
                           required>
                    @error('punto_compra')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="pas-btn pas-btn-primary w-100">
                    <span class="material-symbols-rounded" style="font-size:1rem">send</span>
                    Enviar solicitud
                </button>
            </form>
        </div>

        <p style="font-size:.78rem;color:var(--text-3);margin-top:1.5rem">
            ¿Tienes problemas? Contacta a tu gestor SETP local.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function mostrarPanel(panelId) {
    document.getElementById('panelRegistrar').style.display = 'none';
    document.getElementById('panelComprar').style.display   = 'none';
    document.getElementById('btnRegistrar').classList.remove('selected');
    document.getElementById('btnComprar').classList.remove('selected');

    document.getElementById(panelId).style.display = 'block';
    const btnId = panelId === 'panelRegistrar' ? 'btnRegistrar' : 'btnComprar';
    document.getElementById(btnId).classList.add('selected');
}

// Reabrir panel si hubo error de validación
@if($errors->has('codigo_tarjeta'))
    mostrarPanel('panelRegistrar');
@endif
@if($errors->has('punto_compra'))
    mostrarPanel('panelComprar');
@endif
</script>
@endpush
