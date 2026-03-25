@extends('superadmin.layouts.admin')

@section('title', 'Crear Gestor SETP')



@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.gestores-setp.index') }}">Gestores SETP</a>
            </li>
            <li class="breadcrumb-item active">Nuevo gestor</li>
        </ol>
    </nav>

    <div class="gs-form-wrap">

        {{-- Aviso informativo --}}
        <div class="gs-setp-notice mb-4">
            <span class="material-symbols-rounded">info</span>
            <span>
                Solo se pueden asignar gestores a empresas de tipo <strong>SETP</strong>.
                El rol <strong>Gestor SETP</strong> se asignará automáticamente al crear el usuario.
            </span>
        </div>

        <div class="gs-form-card">
            <div class="gs-form-header">
                <div class="icon-wrap">
                    <span class="material-symbols-rounded">person_add</span>
                </div>
                <div>
                    <h2>Nuevo Gestor SETP</h2>
                    <p>Complete todos los campos obligatorios para registrar el gestor.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('superadmin.gestores-setp.store') }}" novalidate>
                @csrf
                <div class="gs-form-body">

                    {{-- ── Sección 1: Empresa SETP ──────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">business</span>
                        Empresa SETP asignada
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">
                                Empresa SETP <span class="req">*</span>
                            </label>
                            <select name="NIT" class="form-select @error('NIT') is-invalid @enderror" required>
                                <option value="" disabled {{ old('NIT') ? '' : 'selected' }}>
                                    — Seleccione empresa —
                                </option>
                                @foreach($empresasSetp as $emp)
                                <option value="{{ $emp->NIT }}" {{ old('NIT') == $emp->NIT ? 'selected' : '' }}>
                                    {{ $emp->nombre_empresa }} — NIT {{ $emp->NIT }}
                                    ({{ $emp->ciudad->nombre_city ?? 'Sin ciudad' }})
                                </option>
                                @endforeach
                            </select>
                            @error('NIT')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="form-hint">Solo aparecen empresas de tipo Setp registradas en el sistema.</p>
                        </div>
                    </div>

                    {{-- ── Sección 2: Información personal ─────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">person</span>
                        Información personal
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Primer nombre <span class="req">*</span></label>
                            <input type="text" name="primer_nombre"
                                class="form-control @error('primer_nombre') is-invalid @enderror"
                                value="{{ old('primer_nombre') }}"
                                placeholder="Ej: Carlos" maxlength="30" minlength="3" required pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ]{3,30}$" title="Solo letras, mínimo 3 caracteres, sin espacios" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('primer_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo nombre</label>
                            <input type="text" name="segundo_nombre"
                                class="form-control @error('segundo_nombre') is-invalid @enderror"
                                value="{{ old('segundo_nombre') }}"
                                placeholder="Ej: Andrés" maxlength="30" minlength="3" pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ]{3,30}$" title="Solo letras, mínimo 3 caracteres, sin espacios" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('segundo_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primer apellido <span class="req">*</span></label>
                            <input type="text" name="primer_apellido"
                                class="form-control @error('primer_apellido') is-invalid @enderror"
                                value="{{ old('primer_apellido') }}"
                                placeholder="Ej: Ramírez" maxlength="30" minlength="3" required pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ]{3,30}$" title="Solo letras, mínimo 3 caracteres, sin espacios" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('primer_apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo apellido</label>
                            <input type="text" name="segundo_apellido"
                                class="form-control @error('segundo_apellido') is-invalid @enderror"
                                value="{{ old('segundo_apellido') }}"
                                placeholder="Ej: Pérez" maxlength="30" minlength="3" pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ]{3,30}$" title="Solo letras, mínimo 3 caracteres, sin espacios" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('segundo_apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Sección 3: Acceso y contacto ────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">lock</span>
                        Acceso y contacto
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Número de documento <span class="req">*</span></label>
                            <input type="number" name="doc_usuario"
                                class="form-control @error('doc_usuario') is-invalid @enderror"
                                value="{{ old('doc_usuario') }}"
                                placeholder="Ej: 1020304050" required min="1000000" max="9999999999" step="1" pattern="^[0-9]{7,10}$" title="Solo números, entre 7 y 10 dígitos">
                            @error('doc_usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p class="form-hint">Este número es el nombre de usuario para iniciar sesión.</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono"
                                class="form-control @error('telefono') is-invalid @enderror"
                                value="{{ old('telefono') }}"
                                placeholder="Ej: 3001234567" size="10" pattern="^[0-9]{10}$" title="Ingrese numero de telefono de 10 dígitos">
                            @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico <span class="req">*</span></label>
                            <input type="email" name="correo"
                                class="form-control @error('correo') is-invalid @enderror"
                                value="{{ old('correo') }}"
                                placeholder="gestor@setp.gov.co" maxlength="150" required pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" title="Debe ser un correo válido, sin espacios">
                            @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad <span class="req">*</span></label>
                            <select name="id_ciudad" class="form-select @error('id_ciudad') is-invalid @enderror" required>
                                <option value="" disabled {{ old('id_ciudad') ? '' : 'selected' }}>
                                    — Seleccione ciudad —
                                </option>
                                @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad }}"
                                        {{ old('id_ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>
                                    {{ $ciudad->nombre_city }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contraseña <span class="req">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="inputPassword"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="togglePwd"
                                        style="border-color:var(--border);">
                                    <span class="material-symbols-rounded" style="font-size:1rem">visibility</span>
                                </button>
                            </div>
                            @error('password')
                            <div class="text-danger mt-1" style="font-size:.78rem">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña <span class="req">*</span></label>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Repita la contraseña" required minlength="8">
                        </div>
                    </div>

                </div>{{-- /gs-form-body --}}

                <div class="gs-form-footer">
                    <a href="{{ route('superadmin.gestores-setp.index') }}"
                       class="btn btn-outline-secondary"
                       style="border-radius:var(--r-sm)">Cancelar</a>
                    <button type="submit" class="btn d-flex align-items-center gap-2"
                            style="background:var(--p);color:#fff;border-radius:var(--r-sm)">
                        <span class="material-symbols-rounded">save</span>
                        Crear Gestor SETP
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// Mostrar / ocultar contraseña
document.getElementById('togglePwd')?.addEventListener('click', function () {
    const input = document.getElementById('inputPassword');
    const icon  = this.querySelector('.material-symbols-rounded');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
});
</script>
@endpush
