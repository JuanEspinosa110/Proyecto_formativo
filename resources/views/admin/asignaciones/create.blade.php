@extends('admin.layouts.app')

@section('title', 'Nueva Asignación — SIGU')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex align-items-center gap-3 mb-4 mt-2">
                <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-light rounded-circle p-2 shadow-sm d-flex border">
                    <span class="material-symbols-rounded align-middle">arrow_back</span>
                </a>
                <div>
                    <h1 class="h3 mb-0 text-dark fw-bold">Vincular Nueva Asignación</h1>
                    <p class="text-muted small mb-0">Asocia un vehículo y un conductor a una ruta de operación.</p>
                </div>
            </div>

            <!-- Card con Formulario -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Alerta de Errores de Validación -->
                    @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <div class="d-flex align-items-start gap-2">
                            <span class="material-symbols-rounded mt-1">error</span>
                            <div>
                                <strong class="d-block mb-1">Verifique los campos requeridos:</strong>
                                <ul class="mb-0 small ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('admin.asignaciones.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- 1. Ruta -->
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Ruta de operación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">route</span></span>
                                    <select name="id_ruta" class="form-select bg-light border-0 py-2 @error('id_ruta') is-invalid @enderror" required>
                                        <option value="" selected disabled>Seleccionar ruta...</option>
                                        @foreach($rutas as $r)
                                            <option value="{{ $r->id_ruta }}" {{ old('id_ruta') == $r->id_ruta ? 'selected' : '' }}>
                                                #{{ $r->codigo_ruta }} - {{ $r->nombre_ruta ?? 'Ruta' }}
                                                @if($r->concesiones->isEmpty()) (Uso Público) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 2. Fecha y Hora de Inicio -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Fecha y Hora de Inicio <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">calendar_today</span></span>
                                    <input type="datetime-local" name="fecha" id="fecha_asignacion" class="form-control bg-light border-0 py-2 @error('fecha') is-invalid @enderror" value="{{ old('fecha', now()->format('Y-m-d\TH:i')) }}" required>
                                </div>
                            </div>

                            <!-- 3. Hora Fin Estimada + Botón Turno -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Hora Fin (Estimada) <span class="material-symbols-rounded fs-6 align-middle" title="Calculada automáticamente +8h para turnos">help</span></label>
                                <div class="d-flex gap-2">
                                    <div class="input-group flex-fill">
                                        <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">timer</span></span>
                                        <input type="datetime-local" id="fecha_fin_estimada" class="form-control bg-light border-0 py-2 text-muted" readonly disabled>
                                    </div>
                                    <button type="button" id="btn_turno_8h" class="btn btn-outline-primary btn-sm px-3 fw-bold rounded-3 text-nowrap" style="font-size: 0.75rem;">
                                        TURNO 8H
                                    </button>
                                </div>
                            </div>

                            <!-- 4. Bus -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Vehículo asignado (Placa) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">directions_bus</span></span>
                                    <select name="placa" id="select_bus" class="form-select bg-light border-0 py-2 @error('placa') is-invalid @enderror" required disabled title="Seleccione fecha primero">
                                        <option value="" selected disabled>Buscar placa...</option>
                                        {{-- Se llena vía AJAX --}}
                                    </select>
                                </div>
                            </div>

                            <!-- 5. Conductor -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Conductor Responsable <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">person</span></span>
                                    <select name="doc_us" id="select_conductor" class="form-select bg-light border-0 py-2 @error('doc_us') is-invalid @enderror" required disabled title="Seleccione fecha primero">
                                        <option value="" selected disabled>Seleccione un conductor...</option>
                                        {{-- Se llena vía AJAX --}}
                                    </select>
                                </div>
                            </div>

                            <!-- 6. Estado -->
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Estado de la Asignación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><span class="material-symbols-rounded text-muted">info</span></span>
                                    <select name="id_estado" class="form-select bg-light border-0 py-2 @error('id_estado') is-invalid @enderror" required>
                                        @foreach($estados as $e)
                                            <option value="{{ $e->id_estado }}" {{ old('id_estado', 1) == $e->id_estado ? 'selected' : '' }}>
                                                {{ $e->nombre_estado }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="mt-5 d-flex gap-3">
                            <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-light px-4 fw-bold border flex-fill py-2">
                                <span class="material-symbols-rounded align-middle me-1">close</span> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm flex-fill py-2">
                                <span class="material-symbols-rounded align-middle me-1">save</span> Guardar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-text { border-radius: 0.5rem 0 0 0.5rem !important; }
    .form-select, .form-control { border-radius: 0 0.5rem 0.5rem 0 !important; }
    .card { border-radius: 1.5rem !important; }
</style>
<script>
 document.addEventListener('DOMContentLoaded', function() {
     const fechaInput = document.getElementById('fecha_asignacion');
     const fechaFinInput = document.getElementById('fecha_fin_estimada');
     const btnTurno8h = document.getElementById('btn_turno_8h');
     const conductorSelect = document.getElementById('select_conductor');
     const busSelect = document.getElementById('select_bus');
 
     function calcularHoraFin(isoString) {
         if (!isoString) return '';
         const date = new Date(isoString);
         date.setHours(date.getHours() + 8);
         
         const pad = (n) => n.toString().padStart(2, '0');
         return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
     }
 
     function actualizarUI() {
         const fechaValue = fechaInput.value;
         fechaFinInput.value = calcularHoraFin(fechaValue);
 
         if (fechaValue) {
             conductorSelect.disabled = false;
             busSelect.disabled = false;
             conductorSelect.title = "";
             busSelect.title = "";
             actualizarDisponibilidad();
         } else {
             conductorSelect.disabled = true;
             busSelect.disabled = true;
             conductorSelect.title = "Seleccione fecha primero";
             busSelect.title = "Seleccione fecha primero";
         }
     }
 
     function actualizarDisponibilidad() {
         const fechaValue = fechaInput.value;
         if (!fechaValue) return;
 
         conductorSelect.innerHTML = '<option value="" disabled selected>Cargando conductores...</option>';
         busSelect.innerHTML = '<option value="" disabled selected>Cargando buses...</option>';
 
         fetch(`{{ route('empresa.asignaciones.disponibilidad') }}?fecha=${fechaValue}`)
             .then(response => response.json())
             .then(data => {
                 conductorSelect.innerHTML = '<option value="" disabled selected>Seleccione un conductor...</option>';
                 if (data.conductores.length === 0) {
                     conductorSelect.innerHTML = '<option value="" disabled>No hay conductores disponibles</option>';
                 } else {
                     data.conductores.forEach(c => {
                         const option = document.createElement('option');
                         option.value = c.doc_usuario;
                         option.textContent = c.nombre_completo;
                         conductorSelect.appendChild(option);
                     });
                 }
 
                 busSelect.innerHTML = '<option value="" disabled selected>Seleccione un vehículo...</option>';
                 if (data.buses.length === 0) {
                     busSelect.innerHTML = '<option value="" disabled>No hay buses disponibles</option>';
                 } else {
                     data.buses.forEach(b => {
                         const option = document.createElement('option');
                         option.value = b.placa;
                         option.textContent = b.label;
                         busSelect.appendChild(option);
                     });
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 conductorSelect.innerHTML = '<option value="" disabled>Error al cargar</option>';
                 busSelect.innerHTML = '<option value="" disabled>Error al cargar</option>';
             });
     }
 
     fechaInput.addEventListener('change', actualizarUI);
 
     btnTurno8h.addEventListener('click', function() {
         const now = new Date();
         const pad = (n) => n.toString().padStart(2, '0');
         const nowISO = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
         
         fechaInput.value = nowISO;
         actualizarUI();
     });
     
     if (fechaInput.value) {
         actualizarUI();
     }
 });
 </script>
 @endsection
