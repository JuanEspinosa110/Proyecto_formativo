<div class="card border-0 shadow-sm rounded-4 p-4">
    <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-4">
        <span class="material-symbols-rounded text-primary">history</span>
        Ingresos por Trayecto (Últimos viajes)
    </h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3 py-3 text-uppercase small fw-bold border-0"
                        style="background-color: rgba(111, 66, 193, 0.05); color: #6f42c1;">FECHA</th>
                    <th class="py-3 text-uppercase small fw-bold border-0"
                        style="background-color: rgba(111, 66, 193, 0.05); color: #6f42c1;">PLACA</th>
                    <th class="py-3 text-uppercase small fw-bold border-0"
                        style="background-color: rgba(111, 66, 193, 0.05); color: #6f42c1;">RUTA</th>
                    <th class="py-3 text-uppercase small fw-bold border-0 text-center"
                        style="background-color: rgba(111, 66, 193, 0.05); color: #6f42c1;">PASAJEROS</th>
                    <th class="py-3 text-uppercase small fw-bold border-0 text-end pe-3"
                        style="background-color: rgba(111, 66, 193, 0.05); color: #6f42c1;">INGRESO GENERADO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asignacionesGanancias as $asig)
                    <tr>
                        <td class="ps-3 text-muted small">{{ \Carbon\Carbon::parse($asig->fecha)->format('d/m/Y H:i') }}
                        </td>
                        <td><span
                                class="badge bg-primary bg-opacity-10 text-primary border px-2">{{ $asig->placa }}</span>
                        </td>
                        <td class="text-dark fw-medium">{{ $asig->ruta->nombre_ruta ?? 'Ruta Express' }}</td>
                        <td class="text-center">
                            <span
                                class="badge bg-light text-dark border rounded-pill px-2">{{ $asig->ventas->count() }}</span>
                        </td>
                        <td class="text-end pe-3 fw-bold text-success">
                            ${{ number_format($asig->ventas->count() * $precioPasaje) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <span class="material-symbols-rounded fs-1 opacity-25">payments</span>
                            <p class="text-muted mt-2 mb-0">No se han registrado ingresos para este bus aún.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $asignacionesGanancias->appends(['section' => 'ganancias', 'mes_seleccionado' => request('mes_seleccionado')])->links() }}
    </div>
</div>
