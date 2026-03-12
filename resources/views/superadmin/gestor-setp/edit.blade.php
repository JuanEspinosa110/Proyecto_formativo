@extends('superadmin.layouts.admin')

@section('title', 'Editar Gestor SETP')

@push('styles')
<style>
.gs-form-wrap { max-width: 780px; margin: 0 auto; }
.gs-form-card { background:var(--surface);border:1px solid var(--border);border-radius:var(--r-md);box-shadow:var(--sh-sm);overflow:hidden; }
.gs-form-header { padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);background:var(--p-xlight);display:flex;align-items:center;gap:.75rem; }
.gs-form-header .icon-wrap { width:42px;height:42px;background:linear-gradient(135deg,var(--p),var(--p-mid));border-radius:var(--r);display:flex;align-items:center;justify-content:center; }
.gs-form-header .icon-wrap .material-symbols-rounded { color:#fff;font-size:1.3rem;font-variation-settings:var(--ms-on); }
.gs-form-header h2 { font-family:var(--ff-d);font-size:1.1rem;font-weight:700;color:var(--text);margin:0; }
.gs-form-header p { font-size:.8rem;color:var(--text-2);margin:0; }
.gs-form-body { padding:1.75rem 1.5rem; }
.gs-form-footer { padding:1rem 1.5rem;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:.75rem;background:var(--p-xlight); }
.gs-section-title { font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--p);margin:0 0 1rem;padding-bottom:.45rem;border-bottom:2px solid var(--p-light);display:flex;align-items:center;gap:.4rem; }
.gs-section-title .material-symbols-rounded { font-size:.95rem; }
.form-label { font-size:.825rem;font-weight:600;color:var(--text);margin-bottom:.3rem; }
.form-label .req { color:var(--err); }
.form-control,.form-select { border-color:var(--border);border-radius:var(--r-sm);font-size:.875rem;color:var(--text); }
.form-control:focus,.form-select:focus { border-color:var(--p);box-shadow:0 0 0 3px rgba(94,84,142,.12); }
.form-control.is-invalid,.form-select.is-invalid { border-color:var(--err); }
.invalid-feedback { font-size:.78rem;color:var(--err); }
.form-hint { font-size:.76rem;color:var(--text-2);margin-top:.25rem; }
.gs-doc-badge { display:inline-flex;align-items:center;gap:.4rem;background:var(--p-light);color:var(--p);border-radius:var(--r);padding:.35rem .85rem;font-size:.875rem;font-weight:600; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.83rem;">
            <li class="breadcrumb-item">
                <a href="{{ route('superadmin.gestores-setp.index') }}" style="color:var(--p)">Gestores SETP</a>
            </li>
            <li class="breadcrumb-item active">Editar gestor</li>
        </ol>
    </nav>

    <div class="gs-form-wrap">
        <div class="gs-form-card">
            <div class="gs-form-header">
                <div class="icon-wrap">
                    <span class="material-symbols-rounded">edit</span>
                </div>
                <div>
                    <h2>Editar Gestor SETP</h2>
                    <p>
                        Documento:
                        <span class="gs-doc-badge ms-1">
                            <span class="material-symbols-rounded" style="font-size:.9rem">badge</span>
                            {{ number_format($gestor->doc_usuario, 0, '', '.') }}
                        </span>
                    </p>
                </div>
            </div>

            <form method="POST"
                  action="{{ route('superadmin.gestores-setp.update', $gestor->doc_usuario) }}"
                  novalidate>
                @csrf @method('PUT')
                <div class="gs-form-body">

                    {{-- ── Empresa SETP ─────────────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">business</span>
                        Empresa SETP asignada
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">Empresa SETP <span class="req">*</span></label>
                            <select name="NIT" class="form-select @error('NIT') is-invalid @enderror" required>
                                <option value="" disabled>— Seleccione empresa —</option>
                                @foreach($empresasSetp as $emp)
                                <option value="{{ $emp->NIT }}"
                                        {{ old('NIT', $gestor->NIT) == $emp->NIT ? 'selected' : '' }}>
                                    {{ $emp->nombre_empresa }} — NIT {{ $emp->NIT }}
                                    ({{ $emp->ciudad->nombre_city ?? 'Sin ciudad' }})
                                </option>
                                @endforeach
                            </select>
                            @error('NIT')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Información personal ─────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">person</span>
                        Información personal
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Primer nombre <span class="req">*</span></label>
                            <input type="text" name="primer_nombre"
                                class="form-control @error('primer_nombre') is-invalid @enderror"
                                value="{{ old('primer_nombre', $gestor->primer_nombre) }}"
                                maxlength="30" minlength="3" required pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ]{3,30}$" title="Solo letras, mínimo 3 caracteres, sin espacios" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('primer_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo nombre</label>
                            <input type="text" name="segundo_nombre"
                                class="form-control @error('segundo_nombre') is-invalid @enderror"
                                value="{{ old('segundo_nombre', $gestor->segundo_nombre) }}"
                                maxlength="30" minlength="3" pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ]{3,30}$" title="Solo letras, mínimo 3 caracteres, sin espacios" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('segundo_nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primer apellido <span class="req">*</span></label>
                            <input type="text" name="primer_apellido"
                                class="form-control @error('primer_apellido') is-invalid @enderror"
                                value="{{ old('primer_apellido', $gestor->primer_apellido) }}"
                                maxlength="30" minlength="3" required pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ]{3,30}$" title="Solo letras, mínimo 3 caracteres, sin espacios" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('primer_apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Segundo apellido</label>
                            <input type="text" name="segundo_apellido"
                                class="form-control @error('segundo_apellido') is-invalid @enderror"
                                value="{{ old('segundo_apellido', $gestor->segundo_apellido) }}"
                                maxlength="30" minlength="3" pattern="^[A-Za-záéíóúÁÉÍÓÚñÑ]{3,30}$" title="Solo letras, mínimo 3 caracteres, sin espacios" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('segundo_apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Contacto y ciudad ────────────────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">contact_phone</span>
                        Contacto y ubicación
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico <span class="req">*</span></label>
                            <input type="email" name="correo"
                                class="form-control @error('correo') is-invalid @enderror"
                                value="{{ old('correo', $gestor->correo) }}"
                                maxlength="150" required pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" title="Debe ser un correo válido, sin espacios">
                            @error('correo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono"
                                class="form-control @error('telefono') is-invalid @enderror"
                                value="{{ old('telefono', $gestor->telefono) }}"
                                size="10" pattern="^[0-9]{10}$" title="Solo números 10 dígitos">
                            @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ciudad <span class="req">*</span></label>
                            <select name="id_ciudad" class="form-select @error('id_ciudad') is-invalid @enderror" required>
                                <option value="" disabled>— Seleccione ciudad —</option>
                                @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad }}"
                                        {{ old('id_ciudad', $gestor->id_ciudad) == $ciudad->id_ciudad ? 'selected' : '' }}>
                                    {{ $ciudad->nombre_city }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado <span class="req">*</span></label>
                            <select name="id_estado" class="form-select @error('id_estado') is-invalid @enderror" required>
                                <option value="1"  {{ old('id_estado', $gestor->id_estado) == 1  ? 'selected' : '' }}>Activo</option>
                                <option value="2"  {{ old('id_estado', $gestor->id_estado) == 2  ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('id_estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Nueva contraseña (opcional) ─────────── --}}
                    <p class="gs-section-title">
                        <span class="material-symbols-rounded">lock_reset</span>
                        Cambiar contraseña <small style="font-weight:400;font-size:.7rem;text-transform:none;letter-spacing:0">(opcional)</small>
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="inputPassword"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Dejar vacío para no cambiar" minlength="8">
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
                            <label class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   placeholder="Repita la contraseña" minlength="8">
                        </div>
                    </div>

                </div>{{-- /gs-form-body --}}

                <div class="gs-form-footer">
                    <a href="{{ route('superadmin.gestores-setp.index') }}"
                       class="btn btn-outline-secondary"
                       style="border-radius:var(--r-sm)">Cancelar</a>
                    <button type="submit" class="btn d-flex align-items-center gap-2"
                            style="background:var(--p);color:#fff;border-radius:var(--r-sm)">
                        <span class="material-symbols-rounded" style="font-size:1.1rem">save</span>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('togglePwd')?.addEventListener('click', function () {
    const input = document.getElementById('inputPassword');
    const icon  = this.querySelector('.material-symbols-rounded');
    if (input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; }
    else { input.type = 'password'; icon.textContent = 'visibility'; }
});
</script>
@endpush
