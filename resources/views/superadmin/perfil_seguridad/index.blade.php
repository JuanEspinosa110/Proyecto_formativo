@extends('superadmin.layouts.admin')

@section('title', 'Mi Perfil')

@section('content')
<div class="sa-perfil-container">
    <!-- Header -->
    <div class="sa-perfil-header">
        <div>
            <h1 class="sa-perfil-title">Mi Perfil</h1>
            <p class="sa-perfil-subtitle">Gestiona tu información personal y configuración de seguridad</p>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="sa-perfil-alert sa-perfil-alert-success">
        <span class="material-symbols-outlined">check_circle</span>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="sa-perfil-alert sa-perfil-alert-error">
        <span class="material-symbols-outlined">error</span>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    <!-- Sección de Foto de Perfil -->
    <div class="sa-perfil-card">
        <div class="sa-perfil-card-body">
            <div class="sa-perfil-foto-container">
                <div class="sa-perfil-foto-wrapper">
                    @if($superAdmin->foto_perfil)
                        <img src="{{ asset('storage/' . $superAdmin->foto_perfil) }}" 
                             alt="Foto de perfil" 
                             class="sa-perfil-foto"
                             id="fotoPerfil">
                    @else
                        <div class="sa-perfil-foto-placeholder" id="fotoPlaceholder">
                            {{ strtoupper(substr($superAdmin->nombre, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="sa-perfil-foto-info">
                    <h3>{{ $superAdmin->nombre }}</h3>
                    <p>{{ $superAdmin->correo }}</p>
                    <div class="sa-perfil-foto-actions">
                        <label for="inputFoto" class="sa-perfil-file-label">
                            <span class="material-symbols-outlined" style="font-size: 1.25rem;">upload</span>
                            Cambiar Foto
                        </label>
                        <input type="file" 
                               id="inputFoto" 
                               class="sa-perfil-file-input" 
                               accept="image/jpeg,image/png,image/jpg">
                        
                        @if($superAdmin->foto_perfil)
                        <button type="button" 
                                class="sa-perfil-btn sa-perfil-btn-danger"
                                onclick="eliminarFoto()">
                            <span class="material-symbols-outlined" style="font-size: 1.25rem;">delete</span>
                            Eliminar Foto
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Personal -->
    <div class="sa-perfil-card">
        <div class="sa-perfil-card-header">
            <h2 class="sa-perfil-card-title">
                <span class="material-symbols-outlined">person</span>
                Información Personal
            </h2>
            <a href="{{ route('superadmin.perfil.editar-informacion') }}" class="sa-perfil-btn sa-perfil-btn-primary">
                <span class="material-symbols-outlined">edit</span>
                Editar
            </a>
        </div>
        <div class="sa-perfil-card-body">
            <div class="sa-perfil-info-grid">
                <div class="sa-perfil-info-item">
                    <div class="sa-perfil-info-label">Nombre Completo</div>
                    <div class="sa-perfil-info-value">{{ $superAdmin->nombre }}</div>
                </div>
                <div class="sa-perfil-info-item">
                    <div class="sa-perfil-info-label">Correo Electrónico</div>
                    <div class="sa-perfil-info-value">{{ $superAdmin->correo }}</div>
                </div>
                <div class="sa-perfil-info-item">
                    <div class="sa-perfil-info-label">Teléfono</div>
                    <div class="sa-perfil-info-value">{{ $superAdmin->telefono ?? 'No registrado' }}</div>
                </div>
                <div class="sa-perfil-info-item">
                    <div class="sa-perfil-info-label">Documento</div>
                    <div class="sa-perfil-info-value">{{ number_format($superAdmin->doc_super_admin, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Seguridad -->
        <div class="col-lg-4">
            <div class="sa-perfil-card">
                <div class="sa-perfil-card-header">
                    <h2 class="sa-perfil-card-title">
                        <span class="material-symbols-outlined">security</span>
                        Seguridad
                    </h2>
                </div>
                <div class="sa-perfil-card-body">
                    <a href="{{ route('superadmin.perfil.cambiar-contrasena') }}" class="sa-perfil-seguridad-item" style="text-decoration: none;">
                        <div class="sa-perfil-seguridad-info">
                            <h4 class="sa-perfil-seguridad-title">Contraseña</h4>
                            <p class="sa-perfil-seguridad-desc">Actualiza tu contraseña regularmente</p>
                        </div>
                        <span class="material-symbols-outlined" style="color: #007bff; font-size: 1.5rem;">
                            arrow_forward
                        </span>
                    </a>

                    <a href="{{ route('superadmin.perfil.seguridad') }}" class="sa-perfil-seguridad-item" style="text-decoration: none;">
                        <div class="sa-perfil-seguridad-info">
                            <h4 class="sa-perfil-seguridad-title">Opciones de Seguridad</h4>
                            <p class="sa-perfil-seguridad-desc">Gestiona la seguridad de tu cuenta</p>
                        </div>
                        <span class="material-symbols-outlined" style="color: #007bff; font-size: 1.5rem;">
                            arrow_forward
                        </span>
                    </a>

                    <div class="sa-perfil-alert sa-perfil-alert-info" style="margin-top: 1rem; margin-bottom: 0;">
                        <span class="material-symbols-outlined">info</span>
                        <div>
                            <small>Última actualización de seguridad hace 
                                {{ \Carbon\Carbon::parse($superAdmin->updated_at)->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Subir foto de perfil
document.getElementById('inputFoto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('foto', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("superadmin.perfil.actualizar-foto") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al actualizar la foto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar la foto');
    });
});

// Eliminar foto de perfil
function eliminarFoto() {
    if (!confirm('¿Está seguro de eliminar su foto de perfil?')) return;

    fetch('{{ route("superadmin.perfil.eliminar-foto") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al eliminar la foto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la foto');
    });
}
</script>
@endsection
