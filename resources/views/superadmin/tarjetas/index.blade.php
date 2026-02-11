@extends('superadmin.layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@endsection

@section('content')

<div class="tarjetas-wrapper">
    <div class="tarjetas-container">

        <!-- Header -->
        <header class="tarjetas-header">
            <div>
                <h1 class="tarjetas-title">Gestión de Tarjetas</h1>
                <p class="tarjetas-subtitle">
                    Administra el inventario de tarjetas de transporte y su estado.
                </p>
            </div>

            <button class="tarjetas-btn-primary">
                Nueva Tarjeta
            </button>
        </header>

        <!-- KPIs -->
        <section class="tarjetas-kpis">
            <div class="tarjeta-kpi">
                <span>Total Emitidas</span>
                <strong>{{ $totalTarjetas }}</strong>
            </div>

            <div class="tarjeta-kpi kpi-success">
                <span>Activas</span>
                <strong>{{ $tarjetasActivas }}</strong>
            </div>

            <div class="tarjeta-kpi kpi-danger">
                <span>Bloqueadas</span>
                <strong>{{ $tarjetasBloqueadas }}</strong>
            </div>

            <div class="tarjeta-kpi kpi-warning">
                <span>Sin saldo</span>
                <strong>{{ $tarjetasSinSaldo }}</strong>
            </div>
        </section>

        <!-- Tabla -->
        <section class="tarjetas-table-card">
            <table class="tarjetas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Estado</th>
                        <th>Saldo</th>
                        <th>Fecha emisión</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tarjetas as $tarjeta)
                        <tr>
                            <td>{{ $tarjeta->id_tarjeta }}</td>
                            <td>{{ $tarjeta->codigo_tarjeta }}</td>

                            <td>
                                @if ($tarjeta->id_estado == 1)
                                    <span class="estado estado-activa">Activa</span>
                                @elseif ($tarjeta->id_estado == 2)
                                    <span class="estado estado-bloqueada">Inactiva</span>
                                @elseif ($tarjeta->id_estado == 3)
                                    <span class="estado estado-sinsaldo">Sin saldo</span>
                                @endif
                            </td>


                            <td>${{ number_format($tarjeta->saldo, 0, ',', '.') }}</td>


                            <td class="acciones">
                                <a href="{{ route('superadmin.tarjetas.show', $tarjeta->id_tarjeta) }}" class="btn-accion btn-ver">
                                    <i class="bi bi-eye"></i>
                            
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="tabla-vacia">
                                No hay tarjetas registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

    </div>
</div>

@endsection
