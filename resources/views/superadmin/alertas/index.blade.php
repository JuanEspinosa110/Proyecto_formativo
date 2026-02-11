@extends('superadmin.layouts.admin')

@section('title', 'Alertas')

@section('content')

<div class="sa-alertas-wrapper">

<header class="sa-alertas-header">
    <h1 class="sa-alertas-title">
        <i class="bi bi-bell-fill"></i>
        Centro de Alertas
    </h1>

    <p class="sa-alertas-subtitle">
        Supervisión en tiempo real del sistema.
    </p>
</header>

{{-- ================= ALERTAS SISTEMA ================= --}}
<section class="sa-alertas-block">

<h2 class="sa-alertas-block-title">🚨 Alertas del Sistema</h2>

<div class="sa-alertas-list">

@if($documentosVencidos > 0)
<article class="sa-alertas-card grave">
    <h3>Documentación vencida</h3>
    <p>{{ $documentosVencidos }} registros vencidos.</p>
</article>
@endif

@if($mantenimientosProximos > 0)
<article class="sa-alertas-card leve">
    <h3>Mantenimientos próximos</h3>
    <p>{{ $mantenimientosProximos }} buses próximos a revisión.</p>
</article>
@endif

@if($usuariosBloqueados > 0)
<article class="sa-alertas-card grave">
    <h3>Usuarios bloqueados</h3>
    <p>{{ $usuariosBloqueados }} usuarios suspendidos.</p>
</article>
@endif

@if($tarjetasSuspendidas > 0)
<article class="sa-alertas-card leve">
    <h3>Tarjetas suspendidas</h3>
    <p>{{ $tarjetasSuspendidas }} tarjetas inactivas.</p>
</article>
@endif

</div>

</section>

{{-- ================= VIAJES ================= --}}
<section class="sa-alertas-block">

<h2 class="sa-alertas-block-title">🚍 Viajes en tiempo real</h2>

<div class="sa-alertas-list">

<article class="sa-alertas-card info">
    <h3>Viajes activos</h3>
    <p>{{ $viajesActivos }} viajes en curso.</p>
</article>

</div>

</section>

{{-- ================= ATAJOS ================= --}}
<section class="sa-alertas-block">

<h2 class="sa-alertas-block-title">⚡ Atajos rápidos</h2>

<div class="sa-alertas-shortcuts">

<a href="{{ route('superadmin.empresas.index') }}" class="sa-alertas-shortcut-card">
    <i class="bi bi-building-add"></i>
    Empresas
</a>

<a href="{{ route('superadmin.usuarios.index') }}" class="sa-alertas-shortcut-card">
    <i class="bi bi-person-plus"></i>
    Usuarios
</a>

<a href="{{ route('superadmin.reportes.index') }}"
   class="sa-alertas-shortcut-card critical">
    <i class="bi bi-graph-up-arrow"></i>
    Reportes críticos
</a>

</div>

</section>

<section class="sa-alertas-footer">
    <a href="{{ route('superadmin.dashboard') }}"
       class="sa-alertas-btn-back">
        ⬅ Volver
    </a>
</section>

</div>

@endsection
