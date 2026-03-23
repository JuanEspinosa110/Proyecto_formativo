@extends('empresa-recargas.layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')
<div class="admin-dashboard sigu-fade">
    <div class="sigu-page-hd d-flex gap-3 align-items-center mb-4">
        <a href="{{ route('gestor-recargas.usuarios.index') }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center">
            <span class="material-symbols-rounded text-muted">arrow_back</span>
        </a>
        <div>
            <h1 class="sigu-page-title mb-0">Crear Usuario</h1>
            <p class="sigu-page-sub mb-0">Alta de nuevo personal en la empresa</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('gestor-recargas.usuarios.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold text-dark mb-4">Datos Personales</h5>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Documento de Identidad</label>
                        <input type="number" name="doc_usuario" class="form-control @error('doc_usuario') is-invalid @enderror" value="{{ old('doc_usuario') }}" required min="1000000" max="9999999999">
                        @error('doc_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" maxlength="20" pattern="^\+?[0-9]+$" title="Solo números y opcionalmente un signo + al inicio.">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Primer Nombre *</label>
                        <input type="text" name="primer_nombre" class="form-control @error('primer_nombre') is-invalid @enderror" value="{{ old('primer_nombre') }}" required maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$" title="No se permiten espacios ni números.">
                        @error('primer_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Segundo Nombre</label>
                        <input type="text" name="segundo_nombre" class="form-control @error('segundo_nombre') is-invalid @enderror" value="{{ old('segundo_nombre') }}" maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$" title="No se permiten espacios ni números.">
                        @error('segundo_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Primer Apellido *</label>
                        <input type="text" name="primer_apellido" class="form-control @error('primer_apellido') is-invalid @enderror" value="{{ old('primer_apellido') }}" required maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$" title="No se permiten espacios ni números.">
                        @error('primer_apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Segundo Apellido</label>
                        <input type="text" name="segundo_apellido" class="form-control @error('segundo_apellido') is-invalid @enderror" value="{{ old('segundo_apellido') }}" maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$" title="No se permiten espacios ni números.">
                        @error('segundo_apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento') }}" max="{{ now()->subYears(15)->format('Y-m-d') }}">
                        @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="text-muted opacity-25 my-4">

                <h5 class="fw-bold text-dark mb-4">Credenciales de Acceso</h5>
                <p class="text-muted small mb-4">Ingresa el correo electrónico que usará el empleado para iniciar sesión, y asígnale su primera contraseña.</p>

                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <label class="form-label fw-medium">Correo Electrónico (Login) *</label>
                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo') }}" required>
                        @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><span class="material-symbols-rounded fs-5">lock</span></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary border" type="button" id="togglePassword">
                                <span class="material-symbols-rounded fs-5 align-middle" id="toggleIcon">visibility</span>
                            </button>
                        </div>
                        <div class="form-text mt-2 text-muted">
                            <span class="material-symbols-rounded align-middle fs-6 me-1">info</span>
                            Mínimo 8 caracteres: 1 mayúscula, 4 números y 1 carácter especial obligatorio.
                        </div>
                        @error('password') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Confirmar Contraseña *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><span class="material-symbols-rounded fs-5">lock_reset</span></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 ps-0" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-5">
                    <a href="{{ route('gestor-recargas.usuarios.index') }}" class="btn btn-light px-4 rounded-3 text-muted fw-medium">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium d-flex align-items-center gap-2">
                        <span class="material-symbols-rounded fs-5">save</span>
                        Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirmation');
        const toggleIcon = document.getElementById('toggleIcon');

        if (togglePassword) {
            togglePassword.addEventListener('click', function (e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                passwordConfirm.setAttribute('type', type);
                toggleIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
            });
        }

        // 1. Documento de identidad: Solo números, min 7, max 10
        const docInput = document.querySelector('input[name="doc_usuario"]');
        if(docInput) {
            docInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, ''); // Solo números
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
            });
        }

        // 2. Teléfono: Solo números y '+'
        const telInput = document.querySelector('input[name="telefono"]');
        if(telInput) {
            telInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^\+0-9]/g, ''); // Solo números y +
            });
        }

        // 3. Nombres y Apellidos: Sin espacios, sin números, permitir acentos y ñ
        const nameInputs = document.querySelectorAll('input[name="primer_nombre"], input[name="segundo_nombre"], input[name="primer_apellido"], input[name="segundo_apellido"]');
        nameInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '');
            });
        });

        // 4. Correo: Eliminar espacios
        const emailInput = document.querySelector('input[name="correo"]');
        if(emailInput) {
            emailInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\s/g, '');
            });
        }

        // 5. Contraseña: No espacios
        if(password) {
            password.addEventListener('input', function(e) {
                this.value = this.value.replace(/\s/g, '');
            });
        }
    });
</script>
@endpush
@endsection
