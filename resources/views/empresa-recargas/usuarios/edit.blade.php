@extends('empresa-recargas.layouts.app')

@section('title', 'Editar Usuario — Empresa')

@section('content')
<div class="admin-dashboard sigu-fade">
    <div class="sigu-page-hd d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="sigu-page-title">Editar Usuario</h1>
            <p class="sigu-page-sub">Actualiza los datos del cajero o gestor de tu empresa</p>
        </div>
        <div>
            <a href="{{ route('gestor-recargas.usuarios.index') }}" class="btn btn-light d-flex align-items-center gap-2 rounded-3 border">
                <span class="material-symbols-rounded">arrow_back</span>
                Atrás
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('gestor-recargas.usuarios.update', $usuarioEdit->doc_usuario) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Documento de Identidad <small>(No se puede cambiar)</small></label>
                        <input type="text" class="form-control" value="{{ $usuarioEdit->doc_usuario }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $usuarioEdit->telefono) }}" maxlength="20" pattern="^\+?[0-9]+$" title="Solo números y opcionalmente un signo + al inicio.">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Primer Nombre *</label>
                        <input type="text" name="primer_nombre" class="form-control @error('primer_nombre') is-invalid @enderror" value="{{ old('primer_nombre', $usuarioEdit->primer_nombre) }}" required maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$" title="No se permiten espacios ni números.">
                        @error('primer_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Segundo Nombre</label>
                        <input type="text" name="segundo_nombre" class="form-control @error('segundo_nombre') is-invalid @enderror" value="{{ old('segundo_nombre', $usuarioEdit->segundo_nombre) }}" maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$" title="No se permiten espacios ni números.">
                        @error('segundo_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Primer Apellido *</label>
                        <input type="text" name="primer_apellido" class="form-control @error('primer_apellido') is-invalid @enderror" value="{{ old('primer_apellido', $usuarioEdit->primer_apellido) }}" required maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$" title="No se permiten espacios ni números.">
                        @error('primer_apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Segundo Apellido</label>
                        <input type="text" name="segundo_apellido" class="form-control @error('segundo_apellido') is-invalid @enderror" value="{{ old('segundo_apellido', $usuarioEdit->segundo_apellido) }}" maxlength="50" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$" title="No se permiten espacios ni números.">
                        @error('segundo_apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento', $usuarioEdit->fecha_nacimiento) }}" max="{{ now()->subYears(15)->format('Y-m-d') }}">
                        @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Correo Electrónico (Login) *</label>
                        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $usuarioEdit->correo) }}" required>
                        @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3">Cambiar Contraseña</h5>
                <p class="text-muted small mb-4">Si no deseas cambiar la contraseña de este usuario, deja estos campos en blanco.</p>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><span class="material-symbols-rounded fs-5">lock</span></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="Dejar en blanco para mantener la actual">
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
                        <label class="form-label fw-medium">Confirmar Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted border-end-0"><span class="material-symbols-rounded fs-5">lock_reset</span></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 ps-0">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-5 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-lg fw-medium px-5">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirmation');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            passwordConfirm.setAttribute('type', type);
            toggleIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });

        // Validaciones UX nativas
        const telInput = document.querySelector('input[name="telefono"]');
        if(telInput) {
            telInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^\+0-9]/g, '');
            });
        }
        const nameInputs = document.querySelectorAll('input[name="primer_nombre"], input[name="segundo_nombre"], input[name="primer_apellido"], input[name="segundo_apellido"]');
        nameInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ]/g, '');
            });
        });
        const emailInput = document.querySelector('input[name="correo"]');
        if(emailInput) {
            emailInput.addEventListener('input', function() {
                this.value = this.value.replace(/\s/g, '');
            });
        }
        if(password) {
            password.addEventListener('input', function() {
                this.value = this.value.replace(/\s/g, '');
            });
        }
    });
</script>
@endpush
@endsection
