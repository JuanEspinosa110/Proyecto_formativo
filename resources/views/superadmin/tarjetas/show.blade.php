@extends('superadmin.layouts.admin')


@section('content')

<div class="sa-tarjeta">

    <div class="sa-tarjeta__container">

        <!-- HEADER -->
        <div class="sa-tarjeta__header">
            <div class="sa-tarjeta__title">
                <span class="material-symbols-outlined">edit_square</span>
                <h1>Detalle y Edición de Tarjeta</h1>
            </div>

            <button class="sa-tarjeta__close">
                <span class="material-icons">close</span>
            </button>
        </div>

        <!-- BODY -->
        <div class="sa-tarjeta__body">

            <form method="POST" action="{{ route('superadmin.tarjetas.update', $tarjeta->id_tarjeta) }}">
                @csrf
                @method('PUT')

                <div class="sa-tarjeta__grid">

                    <!-- COLUMNA IZQUIERDA -->
                    <div class="sa-tarjeta__left">

                        <!-- DATOS -->
                        <div class="sa-card">
                            <h2 class="sa-section-title">Datos del Dispositivo</h2>

                            <div class="sa-form-grid">

                                <div class="sa-form-group">
                                    <label>ID Tarjeta</label>
                                    <input type="text"
                                        value="{{ $tarjeta->id_tarjeta }}"
                                        readonly>
                                </div>

                                <div class="sa-form-group">
                                    <label>Código de Tarjeta</label>
                                    <input type="text"
                                        name="codigo_tarjeta"
                                        value="{{ $tarjeta->codigo_tarjeta }}">
                                </div>

                                <div class="sa-form-group">
                                    <label>Saldo</label>
                                    <input type="number"
                                        name="saldo"
                                        value="{{ $tarjeta->saldo }}">
                                </div>

                                <div class="sa-form-group">
                                    <label>Estado</label>
                                    <select name="id_estado">
                                        <option value="1" {{ $tarjeta->id_estado == 1 ? 'selected' : '' }}>Activa</option>
                                        <option value="2" {{ $tarjeta->id_estado == 2 ? 'selected' : '' }}>Inactiva</option>
                                        <option value="3" {{ $tarjeta->id_estado == 3 ? 'selected' : '' }}>Suspendida</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- USUARIO ASIGNADO -->
                        <div class="sa-card">
                            <h2 class="sa-section-title">Usuario Asignado</h2>

                            @if(isset($usuario))
                                <div class="sa-user-card">
                                    <div class="sa-user-avatar">
                                        <span class="material-icons">person</span>
                                    </div>
                                    <div>
                                        <p class="sa-user-name">{{ $usuario->nombre }}</p>
                                        <p class="sa-user-rut">{{ $usuario->documento ?? '' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">No hay usuario asignado</p>
                            @endif
                        </div>

                    </div>


                    <!-- COLUMNA DERECHA -->
                    <div class="sa-tarjeta__right">

                        <div class="sa-card sa-table-card">

                            <div class="sa-table-header">
                                <h2 class="sa-section-title">Últimos Movimientos</h2>
                            </div>

                            <div class="sa-table-wrapper">

                                <table class="sa-table">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Ruta / Acción</th>
                                            <th class="text-right">Monto</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($movimientos ?? [] as $mov)
                                            <tr>
                                                <td>{{ $mov->fecha }}</td>
                                                <td>{{ $mov->descripcion }}</td>
                                                <td class="{{ $mov->monto < 0 ? 'text-danger' : 'text-success' }}">
                                                    ${{ number_format($mov->monto, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" style="text-align:center;">
                                                    No hay movimientos registrados
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="sa-tarjeta__footer">
                    <a href="{{ route('superadmin.tarjetas.index') }}"
                    class="sa-btn sa-btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit"
                            class="sa-btn sa-btn-primary">
                        Guardar Cambios
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection
