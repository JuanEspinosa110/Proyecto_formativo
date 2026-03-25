@extends('pasajero.layouts.app')
@section('title', 'Mi perfil')

@section('content')
<div class="container-fluid py-4 px-4">

    <div class="pas-header mb-4">
        <div>
            <h1><span class="material-symbols-rounded">manage_accounts</span> Mi perfil</h1>
            <p>Actualiza tu información personal y tu contraseña de acceso.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <span class="material-symbols-rounded">check_circle</span>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="perfil-wrap">

        {{-- ── Foto de perfil ───────────────────────────────── --}}
        <div class="perfil-card">
            <div class="perfil-card-head">
                <h3><span class="material-symbols-rounded">photo_camera</span> Foto de perfil</h3>
            </div>
            <div class="perfil-card-body text-center">
                <form method="POST" action="{{ route('pasajero.perfil.foto') }}"
                      enctype="multipart/form-data" id="formFoto">
                    @csrf
                    <div class="perfil-avatar-wrap">
                        @if($user->foto_usuario)
                            <img src="{{ asset('storage/' . $user->foto_usuario) }}"
                                 class="perfil-avatar" id="fotoPreview" alt="Foto de perfil">
                        @else
                            <div class="perfil-avatar-placeholder" id="fotoPreview">
                                <span class="material-symbols-rounded">person</span>
                            </div>
                        @endif
                        <label class="perfil-avatar-btn" title="Cambiar foto">
                            <span class="material-symbols-rounded">edit</span>
                            <input type="file" name="foto_usuario" accept="image/*"
                                   onchange="previsualizarFoto(this)">
                        </label>
                    </div>
                    <div style="font-size:.8rem;color:var(--text-2);margin-bottom:.75rem">
                        JPG, PNG o WebP · máx. 2 MB
                    </div>
                    @error('foto_usuario')
                        <div class="pas-alert warn" style="text-align:left">
                            <span class="material-symbols-rounded" style="font-size:1rem">warning</span>
                            {{ $message }}
                        </div>
                    @enderror
                    <button type="submit" class="pas-btn pas-btn-primary" id="btnGuardarFoto" style="display:none">
                        <span class="material-symbols-rounded" style="font-size:1rem">save</span>
                        Guardar foto
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Información personal ──────────────────────── --}}
        <div class="perfil-card">
            <div class="perfil-card-head">
                <h3><span class="material-symbols-rounded">badge</span> Información personal</h3>
            </div>
            <div class="perfil-card-body perfil-form-body">
                <form method="POST" action="{{ route('pasajero.perfil.update') }}" novalidate>
                    @csrf @method('PUT')

                    <p class="perfil-section-title">
                        <span class="material-symbols-rounded">person</span> Datos personales
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Primer nombre <span class="req">*</span></label>
                            <input type="text" name="primer_nombre"
                                   class="form-control @error('primer_nombre') is-invalid @enderror"
                                   value="{{ old('primer_nombre', $user->primer_nombre) }}" required>
                            @error('primer_nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo nombre</label>
                            <input type="text" name="segundo_nombre"
                                   class="form-control"
                                   value="{{ old('segundo_nombre', $user->segundo_nombre) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primer apellido <span class="req">*</span></label>
                            <input type="text" name="primer_apellido"
                                   class="form-control @error('primer_apellido') is-invalid @enderror"
                                   value="{{ old('primer_apellido', $user->primer_apellido) }}" required>
                            @error('primer_apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo apellido</label>
                            <input type="text" name="segundo_apellido"
                                   class="form-control"
                                   value="{{ old('segundo_apellido', $user->segundo_apellido) }}">
                        </div>
                    </div>

                    <p class="perfil-section-title">
                        <span class="material-symbols-rounded">contact_phone</span> Contacto
                    </p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico <span class="req">*</span></label>
                            <input type="email" name="correo"
                                   class="form-control @error('correo') is-invalid @enderror"
                                   value="{{ old('correo', $user->correo) }}" required>
                            @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" name="telefono"
                                   class="form-control"
                                   value="{{ old('telefono', $user->telefono) }}"
                                   placeholder="Ej: 300 123 4567">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="pas-btn pas-btn-primary">
                            <span class="material-symbols-rounded" style="font-size:1rem">save</span>
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Cambio de contraseña ──────────────────────── --}}
        <div class="perfil-card">
            <div class="perfil-card-head">
                <h3><span class="material-symbols-rounded">lock</span> Cambiar contraseña</h3>
            </div>
            <div class="perfil-card-body perfil-form-body">
                <form method="POST" action="{{ route('pasajero.perfil.password') }}" novalidate>
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Contraseña actual <span class="req">*</span></label>
                            <div class="pass-group">
                                <input type="password" name="password_actual" id="passActual"
                                       class="form-control @error('password_actual') is-invalid @enderror"
                                       required>
                                <button type="button" class="pass-toggle" onclick="togglePass('passActual',this)">
                                    <span class="material-symbols-rounded">visibility</span>
                                </button>
                            </div>
                            @error('password_actual')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nueva contraseña <span class="req">*</span></label>
                            <div class="pass-group">
                                <input type="password" name="password" id="passNueva"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required minlength="8">
                                <button type="button" class="pass-toggle" onclick="togglePass('passNueva',this)">
                                    <span class="material-symbols-rounded">visibility</span>
                                </button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <p class="form-hint">Mínimo 8 caracteres.</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmar contraseña <span class="req">*</span></label>
                            <div class="pass-group">
                                <input type="password" name="password_confirmation" id="passConfirm"
                                       class="form-control" required>
                                <button type="button" class="pass-toggle" onclick="togglePass('passConfirm',this)">
                                    <span class="material-symbols-rounded">visibility</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="pas-btn pas-btn-primary">
                            <span class="material-symbols-rounded" style="font-size:1rem">lock_reset</span>
                            Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function previsualizarFoto(input) {
    if (!input.files?.length) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('fotoPreview');
        if (preview.tagName === 'IMG') {
            preview.src = e.target.result;
        } else {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'perfil-avatar';
            img.id = 'fotoPreview';
            preview.replaceWith(img);
        }
        document.getElementById('btnGuardarFoto').style.display = 'inline-flex';
    };
    reader.readAsDataURL(input.files[0]);
}

function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('.material-symbols-rounded');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endpush
