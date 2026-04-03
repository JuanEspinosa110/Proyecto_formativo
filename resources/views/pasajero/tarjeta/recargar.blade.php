@extends('pasajero.layouts.app')

@section('title', 'Recargar Tarjeta SIGU')

@section('content')
<div class="container py-4 recharge-page">
    <!-- Header Minimalista -->
    <div class="mb-5 text-center">
        <h2 class="fw-black text-dark mb-2">Recargar Saldo</h2>
        <p class="text-muted small">Selecciona el monto que deseas abonar a tu tarjeta SIGU.</p>
    </div>

    <!-- Alertas de Validación -->
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center gap-3">
            <span class="material-symbols-rounded">error</span>
            <div>
                @foreach ($errors->all() as $error)
                    <div class="small fw-bold">{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form action="{{ route('pasajero.tarjeta.procesarRecarga') }}" method="POST" id="recarga-form">
        @csrf
        <input type="hidden" name="id_tarjeta" value="{{ $tarjeta->id_tarjeta }}">
        <input type="hidden" name="monto" id="monto_input" required>

        <!-- Grilla de Montos -->
        <label class="form-label small fw-bold text-muted text-uppercase mb-3 px-1">Montos Sugeridos</label>
        <div class="row g-3 mb-4">
            @foreach([5000, 10000, 20000, 30000, 50000] as $valor)
                <div class="col-6 col-md-4">
                    <div class="amount-card p-3 text-center rounded-4 border shadow-sm transition-all cursor-pointer bg-white" 
                         onclick="selectAmount(this, {{ $valor }})">
                        <span class="d-block x-small fw-bold text-muted text-uppercase mb-1">Recargar</span>
                        <h4 class="fw-black text-dark mb-0">${{ number_format($valor, 0, ',', '.') }}</h4>
                    </div>
                </div>
            @endforeach

            <!-- Toggle para montos superiores -->
            <div class="col-12 text-center mt-2">
                <button type="button" id="toggle-more" class="btn btn-link text-decoration-none text-primary small fw-bold d-flex align-items-center justify-content-center gap-2 mx-auto">
                    <span class="material-symbols-rounded fs-5">expand_more</span>
                    Ver montos superiores
                </button>
            </div>

            <!-- Panel de montos superiores (Oculto inicialmente) -->
            <div id="more-amounts" class="col-12" style="display: none;">
                <div class="row g-3 mt-1">
                    @foreach([100000, 200000, 300000] as $valor)
                        <div class="col-6 col-md-4">
                            <div class="amount-card p-3 text-center rounded-4 border shadow-sm transition-all cursor-pointer bg-white border-warning-subtle" 
                                 onclick="selectAmount(this, {{ $valor }})">
                                <span class="d-block x-small fw-bold text-muted text-uppercase mb-1 text-warning">Especial</span>
                                <h4 class="fw-black text-dark mb-0">${{ number_format($valor, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Resumen y Acción -->
        <div class="selection-summary card border-0 shadow-lg rounded-4 overflow-hidden mb-4 p-4 text-center d-none" id="summary-section">
            <p class="text-muted small mb-1">Monto seleccionado para pagar:</p>
            <h2 class="fw-black text-primary display-6 mb-3" id="display-amount">$0</h2>
            <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                <span class="material-symbols-rounded">credit_card</span>
                Continuar Pago Seguro
            </button>
        </div>

        <div class="text-center">
            <p class="text-muted x-small">
                <span class="material-symbols-rounded fs-6 align-middle">lock</span>
                Pagos procesados de forma segura por <strong>Stripe</strong>
            </p>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function selectAmount(element, value) {
        // Remover activos previos
        document.querySelectorAll('.amount-card').forEach(el => el.classList.remove('active'));
        
        // Marcar actual
        element.classList.add('active');
        
        // Actualizar inputs
        document.getElementById('monto_input').value = value;
        document.getElementById('display-amount').innerText = '$' + value.toLocaleString('es-CO');
        
        // Mostrar resumen
        const summary = document.getElementById('summary-section');
        summary.classList.remove('d-none');
        summary.scrollIntoView({ behavior: 'smooth', block: 'end' });
    }

    document.getElementById('toggle-more').addEventListener('click', function() {
        const moreSection = document.getElementById('more-amounts');
        const isHidden = moreSection.style.display === 'none';
        
        if (isHidden) {
            moreSection.style.display = 'block';
            this.innerHTML = '<span class="material-symbols-rounded fs-5">expand_less</span> Ocultar montos superiores';
        } else {
            moreSection.style.display = 'none';
            this.innerHTML = '<span class="material-symbols-rounded fs-5">expand_more</span> Ver montos superiores';
        }
    });
</script>
@endpush
@endsection
