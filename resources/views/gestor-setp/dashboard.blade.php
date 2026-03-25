@extends('gestor-setp.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Alertas de sesión --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">check_circle</span>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Bienvenida ──────────────────────────────────────────── --}}
    <div class="dash-welcome">
        <span class="material-symbols-rounded dash-welcome-icon">route</span>
        <h2>Bienvenido, {{ auth()->user()->primer_nombre ?? 'Gestor' }} 👋</h2>
        <p>
            Panel de control del módulo Gestor SETP ·
            {{ auth()->user()->ciudad->nombre_city ?? 'Ciudad' }}
        </p>
    </div>

    {{-- ── Estadísticas ─────────────────────────────────────────── --}}
    <div class="dash-stats">
        <div class="dash-stat">
            <div class="dash-stat-icon acc">
                <span class="material-symbols-rounded">alt_route</span>
            </div>
            <div>
                <div class="dash-stat-val">{{ $totalRutas ?? 0 }}</div>
                <div class="dash-stat-lbl">Rutas registradas</div>
            </div>
        </div>
        <div class="dash-stat">
            <div class="dash-stat-icon green">
                <span class="material-symbols-rounded">business</span>
            </div>
            <div>
                <div class="dash-stat-val">{{ $totalEmpresas ?? 0 }}</div>
                <div class="dash-stat-lbl">Empresas de transporte</div>
            </div>
        </div>
        <div class="dash-stat">
            <div class="dash-stat-icon acc">
                <span class="material-symbols-rounded">directions_bus</span>
            </div>
            <div>
                <div class="dash-stat-val">{{ $totalBuses ?? 0 }}</div>
                <div class="dash-stat-lbl">Buses activos</div>
            </div>
        </div>
        <div class="dash-stat">
            <div class="dash-stat-icon warn">
                <span class="material-symbols-rounded">folder_off</span>
            </div>
            <div>
                <div class="dash-stat-val">{{ $busesDocsPendientes ?? 0 }}</div>
                <div class="dash-stat-lbl">Buses con docs. pendientes</div>
            </div>
        </div>
        <div class="dash-stat">
            <div class="dash-stat-icon err">
                <span class="material-symbols-rounded">event_busy</span>
            </div>
            <div>
                <div class="dash-stat-val">{{ $docsVencidos ?? 0 }}</div>
                <div class="dash-stat-lbl">Documentos vencidos</div>
            </div>
        </div>
    </div>

    {{-- ── Alertas de documentos + Rutas recientes ─────────────── --}}
    <div class="dash-row">

        {{-- Alertas de documentos --}}
        <div class="dash-section">
            <div class="dash-section-head">
                <h3>
                    <span class="material-symbols-rounded">warning</span>
                    Alertas de documentación
                </h3>
                <a href="{{ route('gestor-setp.documentos.index', ['estado_doc' => 'vencidos']) }}"
                   class="dash-view-all">
                    Ver todos <span class="material-symbols-rounded" style="font-size:.85rem">arrow_forward</span>
                </a>
            </div>
            <div class="dash-alert-list">
                @forelse($alertasDocumentos ?? [] as $alerta)
                <div class="dash-alert-item">
                    <div class="dash-alert-dot {{ $alerta['tipo'] == 'vencido' ? 'err' : 'warn' }}"></div>
                    <div>
                        <div class="dash-alert-text">
                            Bus <strong>{{ $alerta['placa'] }}</strong> —
                            Doc: {{ $alerta['nombre_doc'] }}
                        </div>
                        <div class="dash-alert-meta">
                            {{ $alerta['empresa'] }} ·
                            {{ $alerta['tipo'] == 'vencido' ? 'Vencido el' : 'Vence el' }}
                            {{ \Carbon\Carbon::parse($alerta['fecha_vencimiento'])->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="dash-empty-mini">
                    <span class="material-symbols-rounded" style="display:block;font-size:1.8rem;color:var(--ok);margin-bottom:.3rem">check_circle</span>
                    Toda la documentación está al día. ✓
                </div>
                @endforelse
            </div>
        </div>

        {{-- Rutas recientes --}}
        <div class="dash-section">
            <div class="dash-section-head">
                <h3>
                    <span class="material-symbols-rounded">alt_route</span>
                    Rutas recientes
                </h3>
                <a href="{{ route('gestor-setp.rutas.index') }}" class="dash-view-all">
                    Ver todas <span class="material-symbols-rounded" style="font-size:.85rem">arrow_forward</span>
                </a>
            </div>
            <div>
                @forelse($rutasRecientes ?? [] as $ruta)
                <div class="dash-ruta-item">
                    <div>
                        <div class="dash-ruta-code">Ruta #{{ $ruta->codigo_ruta }}</div>
                        <div class="dash-ruta-trayecto">
                            {{ $ruta->barrioOrigen->nombre ?? '?' }}
                            → {{ $ruta->barrioDestino->nombre ?? '?' }}
                        </div>
                    </div>
                    <span class="dash-mini-badge {{ $ruta->id_estado == 1 ? 'ok' : 'err' }}">
                        {{ $ruta->id_estado == 1 ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
                @empty
                <div class="dash-empty-mini">No hay rutas registradas aún.</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── Accesos rápidos ──────────────────────────────────────── --}}
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-md);padding:1.25rem;box-shadow:var(--sh-xs)">
        <p style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-2);margin:0 0 1rem;display:flex;align-items:center;gap:.4rem">
            <span class="material-symbols-rounded" style="font-size:1rem;color:var(--acc)">flash_on</span>
            Accesos rápidos
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:.75rem">
            <a href="{{ route('gestor-setp.rutas.create') }}"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.1rem;background:var(--acc);color:#fff;border-radius:var(--r-sm);font-size:.85rem;font-weight:600;text-decoration:none;transition:background .15s">
                <span class="material-symbols-rounded" style="font-size:1rem">add_road</span>
                Nueva ruta
            </a>
            <a href="{{ route('gestor-setp.documentos.index', ['estado_doc' => 'vencidos']) }}"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.1rem;background:var(--err-bg);color:var(--err);border:1px solid var(--err);border-radius:var(--r-sm);font-size:.85rem;font-weight:600;text-decoration:none;transition:all .15s">
                <span class="material-symbols-rounded" style="font-size:1rem">event_busy</span>
                Docs. vencidos
            </a>
            <a href="{{ route('gestor-setp.buses.index', ['docs' => 'pendientes']) }}"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.1rem;background:var(--warn-bg);color:var(--warn);border:1px solid var(--warn);border-radius:var(--r-sm);font-size:.85rem;font-weight:600;text-decoration:none;transition:all .15s">
                <span class="material-symbols-rounded" style="font-size:1rem">no_transfer</span>
                Buses con pendientes
            </a>
            <a href="{{ route('gestor-setp.empresas.index') }}"
               style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.1rem;background:var(--acc-l);color:var(--acc);border:1px solid var(--acc);border-radius:var(--r-sm);font-size:.85rem;font-weight:600;text-decoration:none;transition:all .15s">
                <span class="material-symbols-rounded" style="font-size:1rem">business</span>
                Ver empresas
            </a>
        </div>
    </div>

</div>
@endsection
