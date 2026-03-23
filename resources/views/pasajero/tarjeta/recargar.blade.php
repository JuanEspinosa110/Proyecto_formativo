@extends('pasajero.layouts.app')
@section('title', 'Recargar con Stripe')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Recargar saldo con Stripe</h2>
    <form action="{{ route('pasajero.tarjeta.procesarRecarga') }}" method="POST" id="recarga-form">
        @csrf
        <input type="hidden" name="id_tarjeta" value="{{ $tarjeta->id_tarjeta }}">
        <div class="mb-3">
            <label for="monto" class="form-label">Monto a recargar (COP)</label>
            <input type="number" min="1000" step="100" class="form-control" id="monto" name="monto" required>
        </div>
        <button type="submit" class="btn btn-primary">Pagar con Stripe</button>
    </form>
</div>
@endsection
