@extends('gestor-setp.layouts.app')

@section('title', 'Documentos')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Alertas --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">check_circle</span>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">error</span>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">warning</span>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Encabezado --}}
    <div class="doc-header">
        <div>
            <h1>
                <span class="material-symbols-rounded" style="color:var(--acc)">folder_open</span>
                Documentos de buses
            </h1>
            <p>Revisión de documentación de los buses. Puedes enviar avisos y gestionar incumplimientos.</p>
        </div>
    </div>

    {{-- Banner informativo --}}
    <div class="doc-info-banner">
        <span class="material-symbols-rounded" style="flex-shrink:0">info</span>
        <span>
            Si un bus tiene documentos <strong>vencidos o faltantes</strong>, puedes enviar un aviso a la empresa.
            Si la empresa no regulariza en el plazo establecido, tienes la opción de <strong>inactivar el bus</strong>.
        </span>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('gestor-setp.documentos.index') }}" class="doc-filters">
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Placa</label>
            <input type="text" name="placa" class="form-control" placeholder="Ej: BUS567"
                   value="{{ request('placa') }}" style="width:130px">
        </div>
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Empresa</label>
            <select name="nit" class="form-select" style="min-width:200px">
                <option value="">Todas</option>
                @foreach($empresas as $emp)
                <option value="{{ $emp->NIT }}" {{ request('nit') == $emp->NIT ? 'selected' : '' }}>
                    {{ $emp->nombre_empresa }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-2)">Estado docs.</label>
            <select name="estado_doc" class="form-select" style="min-width:160px">
                <option value="">Todos</option>
                <option value="vencidos"   {{ request('estado_doc')=='vencidos'  ?'selected':'' }}>Vencidos</option>
                <option value="por_vencer" {{ request('estado_doc')=='por_vencer'?'selected':'' }}>Por vencer (30 días)</option>
                <option value="vigentes"   {{ request('estado_doc')=='vigentes'  ?'selected':'' }}>Vigentes</option>
            </select>
        </div>
        <button type="submit" class="btn d-flex align-items-center gap-1"
                style="background:var(--acc);color:#fff;border-radius:var(--r-sm);align-self:flex-end;font-size:.875rem">
            <span class="material-symbols-rounded" style="font-size:1rem">search</span> Filtrar
        </button>
        @if(request()->hasAny(['placa','nit','estado_doc']))
        <a href="{{ route('gestor-setp.documentos.index') }}"
           class="btn btn-outline-secondary d-flex align-items-center gap-1"
           style="border-radius:var(--r-sm);align-self:flex-end;font-size:.875rem">
            <span class="material-symbols-rounded" style="font-size:1rem">close</span> Limpiar
        </a>
        @endif
    </form>

    {{-- Listado agrupado por bus --}}
    @forelse($buses as $bus)
    @php
        $docsVencidos  = $bus->documentos->filter(fn($d) => \Carbon\Carbon::parse($d->fecha_vencimiento)->isPast())->count();
        $docsPorVencer = $bus->documentos->filter(fn($d) => !(\Carbon\Carbon::parse($d->fecha_vencimiento)->isPast()) && \Carbon\Carbon::parse($d->fecha_vencimiento)->diffInDays(now()) <= 30)->count();
        $hayProblemas  = $docsVencidos > 0 || $docsPorVencer > 0;
    @endphp

    <div class="doc-bus-section">
        {{-- Cabecera del bus --}}
        <div class="doc-bus-header">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="doc-bus-placa">{{ $bus->placa }}</span>
                <div>
                    <div class="fw-semibold">{{ $bus->empresa->nombre_empresa ?? '—' }}</div>
                    <div class="doc-bus-empresa">NIT: {{ $bus->NIT }} · Modelo: {{ $bus->modelo ?? '—' }}</div>
                </div>
                @if($docsVencidos > 0)
                    <span class="doc-bus-alerta warn">
                        <span class="material-symbols-rounded" style="font-size:.9rem">warning</span>
                        {{ $docsVencidos }} doc(s) vencido(s)
                    </span>
                @elseif($docsPorVencer > 0)
                    <span class="doc-bus-alerta warn">
                        <span class="material-symbols-rounded" style="font-size:.9rem">schedule</span>
                        {{ $docsPorVencer }} doc(s) por vencer
                    </span>
                @else
                    <span class="doc-bus-alerta ok">
                        <span class="material-symbols-rounded" style="font-size:.9rem">check_circle</span>
                        Documentación al día
                    </span>
                @endif
            </div>

            {{-- Acciones rápidas por bus --}}
            <div class="doc-bus-actions">
                @if($hayProblemas)
                <button class="doc-btn doc-btn-warn"
                        data-bs-toggle="modal"
                        data-bs-target="#modalAviso"
                        data-placa="{{ $bus->placa }}"
                        data-empresa="{{ $bus->empresa->nombre_empresa ?? $bus->NIT }}"
                        data-correo="{{ $bus->empresa->correo_corporativo ?? '' }}">
                    <span class="material-symbols-rounded" style="font-size:1rem">notification_important</span>
                    Enviar aviso
                </button>

                @if($bus->id_estado == 1)
                <button class="doc-btn doc-btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#modalInactivar"
                        data-placa="{{ $bus->placa }}"
                        data-empresa="{{ $bus->empresa->nombre_empresa ?? $bus->NIT }}">
                    <span class="material-symbols-rounded" style="font-size:1rem">no_transfer</span>
                    Inactivar bus
                </button>
                @endif
                @endif

                <a href="{{ route('gestor-setp.buses.show', $bus->placa) }}" class="doc-btn doc-btn-view">
                    <span class="material-symbols-rounded" style="font-size:1rem">visibility</span>
                    Ver bus
                </a>
            </div>
        </div>

        {{-- Documentos del bus --}}
        <div class="doc-table-wrap">
            @if($bus->documentos->count())
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Tipo</th>
                        <th>Expedición</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th>Archivo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bus->documentos as $doc)
                    @php
                        $vence = \Carbon\Carbon::parse($doc->fecha_vencimiento);
                        $hoy   = now();
                        $dias  = $hoy->diffInDays($vence, false);
                        $claseVence = $dias < 0 ? 'doc-vence-exp' : ($dias <= 30 ? 'doc-vence-warn' : 'doc-vence-ok');
                        $iconVence  = $dias < 0 ? 'event_busy' : ($dias <= 30 ? 'event_note' : 'event_available');
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $doc->nombre }}</div>
                            <div style="font-size:.74rem;color:var(--text-2)">ID: {{ $doc->id_documento }}</div>
                        </td>
                        <td>{{ $doc->tipoDocumento->nombre ?? '—' }}</td>
                        <td style="font-size:.82rem">
                            {{ \Carbon\Carbon::parse($doc->fecha_expedicion)->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="{{ $claseVence }} d-flex align-items-center gap-1">
                                <span class="material-symbols-rounded" style="font-size:.9rem">{{ $iconVence }}</span>
                                {{ $vence->format('d/m/Y') }}
                                @if($dias < 0)
                                    <small>(Vencido)</small>
                                @elseif($dias <= 30)
                                    <small>({{ $dias }}d)</small>
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($dias < 0)
                                <span class="doc-badge doc-badge-err">
                                    <span class="material-symbols-rounded" style="font-size:.8rem">cancel</span> Vencido
                                </span>
                            @elseif($dias <= 30)
                                <span class="doc-badge doc-badge-warn">
                                    <span class="material-symbols-rounded" style="font-size:.8rem">schedule</span> Por vencer
                                </span>
                            @else
                                <span class="doc-badge doc-badge-ok">
                                    <span class="material-symbols-rounded" style="font-size:.8rem">check_circle</span> Vigente
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($doc->archivo)
                            <a href="{{ asset($doc->archivo) }}" target="_blank"
                               class="doc-btn doc-btn-view" style="font-size:.76rem;padding:.2rem .5rem">
                                <span class="material-symbols-rounded" style="font-size:.9rem">open_in_new</span> Ver
                            </a>
                            @else
                                <span style="color:var(--text-3);font-size:.8rem">Sin archivo</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="doc-empty">
                <span class="material-symbols-rounded">folder_off</span>
                <p>Este bus no tiene documentos registrados.</p>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-md);padding:3.5rem 1rem;text-align:center">
        <span class="material-symbols-rounded" style="font-size:3rem;color:var(--text-3);display:block;margin-bottom:.75rem">folder_open</span>
        <p style="color:var(--text-2);font-size:.9rem;margin:0">No se encontraron buses con los filtros aplicados.</p>
    </div>
    @endforelse

    {{-- Paginación --}}
    @if(isset($buses) && method_exists($buses, 'hasPages') && $buses->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $buses->appends(request()->query())->links() }}
    </div>
    @endif

</div>

{{-- ══ Modal: Enviar aviso a empresa ══════════════════════════ --}}
<div class="modal fade" id="modalAviso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--r-md);overflow:hidden">
            <div style="background:var(--warn-bg);border-bottom:1px solid var(--border);padding:1rem 1.25rem;display:flex;align-items:center;gap:.5rem">
                <span class="material-symbols-rounded" style="color:var(--warn)">notification_important</span>
                <h6 class="modal-title fw-bold mb-0" style="color:var(--warn)">
                    Enviar aviso a empresa — Bus <span id="modal-aviso-placa"></span>
                </h6>
            </div>
            <form id="formAviso" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert d-flex align-items-start gap-2"
                         style="background:var(--warn-bg);border:1px solid var(--warn);border-radius:var(--r);font-size:.84rem;color:var(--warn)">
                        <span class="material-symbols-rounded" style="flex-shrink:0">warning</span>
                        <span>
                            Se enviará un aviso a la empresa <strong id="modal-aviso-empresa"></strong> informando
                            que el bus <strong id="modal-aviso-placa2"></strong> tiene documentación
                            pendiente o vencida. La empresa deberá regularizar la situación en el plazo indicado.
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem">
                            Plazo para regularizar <span style="color:var(--err)">*</span>
                        </label>
                        <input type="date" name="plazo_regularizacion" class="form-control"
                               min="{{ now()->addDay()->format('Y-m-d') }}" required
                               value="{{ now()->addDays(15)->format('Y-m-d') }}">
                        <p style="font-size:.75rem;color:var(--text-2);margin-top:.25rem">
                            Fecha límite para que la empresa registre los documentos. Si no cumple, el bus podrá ser inactivado.
                        </p>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:.85rem">Mensaje adicional</label>
                        <textarea name="mensaje" class="form-control" rows="3"
                                  placeholder="Descripción de los documentos faltantes o información adicional…"
                                  maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                            style="border-radius:var(--r-sm)">Cancelar</button>
                    <button type="submit" class="btn btn-sm d-flex align-items-center gap-1"
                            style="background:var(--warn);color:#fff;border-radius:var(--r-sm)">
                        <span class="material-symbols-rounded" style="font-size:1rem">send</span>
                        Enviar aviso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ Modal: Inactivar bus por incumplimiento ══════════════════ --}}
<div class="modal fade" id="modalInactivar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--r-md);overflow:hidden">
            <div style="background:var(--err-bg);border-bottom:1px solid var(--border);padding:1rem 1.25rem;display:flex;align-items:center;gap:.5rem">
                <span class="material-symbols-rounded" style="color:var(--err)">no_transfer</span>
                <h6 class="modal-title fw-bold mb-0" style="color:var(--err)">
                    Inactivar bus — <span id="modal-inact-placa"></span>
                </h6>
            </div>
            <form id="formInactivar" method="POST" action="">
                @csrf @method('PATCH')
                <div class="modal-body p-4">
                    <div class="alert d-flex align-items-start gap-2"
                         style="background:var(--err-bg);border:1px solid var(--err);border-radius:var(--r);font-size:.84rem;color:var(--err)">
                        <span class="material-symbols-rounded" style="flex-shrink:0">warning</span>
                        <span>
                            Está a punto de <strong>inactivar</strong> el bus <strong id="modal-inact-placa2"></strong>
                            de la empresa <strong id="modal-inact-empresa"></strong> por incumplimiento documental.
                            Esta acción cambiará el estado del bus a <strong>Inactivo</strong>.
                        </span>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:.85rem">
                            Motivo de inactivación <span style="color:var(--err)">*</span>
                        </label>
                        <textarea name="motivo" class="form-control" rows="3" required
                                  placeholder="Ej: Documentos SOAT y Revisión Técnica vencidos sin regularizar tras aviso enviado el…"
                                  maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"
                            style="border-radius:var(--r-sm)">Cancelar</button>
                    <button type="submit" class="btn btn-sm d-flex align-items-center gap-1"
                            style="background:var(--err);color:#fff;border-radius:var(--r-sm)">
                        <span class="material-symbols-rounded" style="font-size:1rem">no_transfer</span>
                        Confirmar inactivación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Modal aviso
document.getElementById('modalAviso').addEventListener('show.bs.modal', function (e) {
    const btn     = e.relatedTarget;
    const placa   = btn.getAttribute('data-placa');
    const empresa = btn.getAttribute('data-empresa');
    document.getElementById('modal-aviso-placa').textContent  = placa;
    document.getElementById('modal-aviso-placa2').textContent = placa;
    document.getElementById('modal-aviso-empresa').textContent = empresa;
    document.getElementById('formAviso').action =
        '{{ url("/gestor-setp/documentos/avisar") }}/' + placa;
});

// Modal inactivar
document.getElementById('modalInactivar').addEventListener('show.bs.modal', function (e) {
    const btn     = e.relatedTarget;
    const placa   = btn.getAttribute('data-placa');
    const empresa = btn.getAttribute('data-empresa');
    document.getElementById('modal-inact-placa').textContent  = placa;
    document.getElementById('modal-inact-placa2').textContent = placa;
    document.getElementById('modal-inact-empresa').textContent = empresa;
    document.getElementById('formInactivar').action =
        '{{ url("/gestor-setp/documentos/inactivar-bus") }}/' + placa;
});
</script>
@endpush
