@extends('superadmin.layouts.admin')

@section('title', 'Detalles de Empresa')

@section('content')
<div class="empresa-container">
    
    {{-- HEADER --}}
    <div class="empresa-header">
        <div class="empresa-header-title">
            <a href="{{ route('superadmin.empresas.index') }}" class="btn-back">
                <span class="material-symbols-outlined"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>
            </a>
            <div>
                <h1><span class="material-symbols-outlined"><i class="fa fa-building" aria-hidden="true"></i></span> {{ $empresa->nombre_empresa }}</h1>
                <p>NIT: {{ number_format($empresa->NIT, 0, '', '.') }}</p>
            </div>
        </div>
        <div class="empresa-header-actions">
            <a href="{{ route('superadmin.empresas.edit', $empresa->NIT) }}" class="btn btn-primary">
                <span class="material-symbols-outlined"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                Editar
            </a>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- INFORMACIÓN GENERAL --}}
        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-card-header">
                    <span class="material-symbols-outlined"><i class="fa fa-building" aria-hidden="true"></i></span>
                    <h3>Información de la Empresa</h3>
                </div>
                <div class="detail-card-body">
                    <div class="detail-item">
                        <label>NIT:</label>
                        <span>{{ number_format($empresa->NIT, 0, '', '.') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Nombre:</label>
                        <span>{{ $empresa->nombre_empresa }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Teléfono:</label>
                        <span>{{ $empresa->telefono_empresa ?? 'No registrado' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Correo Corporativo:</label>
                        <span>{{ $empresa->correo_corporativo ?? 'No registrado' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Estado:</label>
                        @if($empresa->estado)
                            <span class="badge-estado {{ strtolower($empresa->estado->nombre_estado) }}">
                                {{ $empresa->estado->nombre_estado }}
                            </span>
                        @endif
                    </div>
                    <div class="detail-item">
                        <label>Fecha de Registro:</label>
                        <span>{{ \Carbon\Carbon::parse($empresa->fecha_creacion)->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- REPRESENTANTE LEGAL --}}
        <div class="col-md-6">
            <div class="detail-card">
                <div class="detail-card-header">
                    <span class="material-symbols-outlined"><i class="fa fa-user" aria-hidden="true"></i></span>
                    <h3>Representante Legal</h3>
                </div>
                <div class="detail-card-body">
                    <div class="detail-item">
                        <label>Documento:</label>
                        <span>{{ number_format($empresa->doc_representante, 0, '', '.') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Nombre Completo:</label>
                        <span>{{ $empresa->nombre_completo_representante }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Teléfono:</label>
                        <span>{{ $empresa->telefono_representante ?? 'No registrado' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Correo:</label>
                        <span>{{ $empresa->correo_representante ?? 'No registrado' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- UBICACIÓN --}}
        <div class="col-md-12">
            <div class="detail-card">
                <div class="detail-card-header">
                    <span class="material-symbols-outlined"><i class="fa fa-map" aria-hidden="true"></i></span>
                    <h3>Ubicación</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <label>Departamento:</label>
                                <span>{{ $empresa->ciudad->departamento->nombre_departamento ?? 'No registrado' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <label>Ciudad:</label>
                                <span>{{ $empresa->ciudad->nombre_city ?? 'No registrada' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ESTADÍSTICAS --}}
        <div class="col-md-12">
            <div class="detail-card">
                <div class="detail-card-header">
                    <span class="material-symbols-outlined"><i class="fa fa-line-chart" aria-hidden="true"></i>
                        </span>
                    <h3>Estadísticas</h3>
                </div>
                <div class="detail-card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-icon">
                                    <span class="material-symbols-outlined"><i class="fa fa-bus" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-icon">
                                    <span class="material-symbols-outlined"><i class="fa fa-users" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div class="stat-content">
                                    <h4>{{ $empresa->usuarios->count() }}</h4>
                                    <p>Usuarios</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-icon">
                                    <span class="material-symbols-outlined"><i class="fa fa-map-marker" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div class="stat-content">
                                    <h4>0</h4>
                                    <p>Rutas Activas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box">
                                <div class="stat-icon">
                                    <span class="material-symbols-outlined"><i class="fa fa-ticket" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <div class="stat-content">
                                    <h4>0</h4>
                                    <p>Viajes Hoy</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- USUARIOS DE LA EMPRESA --}}
        @if($empresa->usuarios->count() > 0)
        <div class="col-md-12">
            <div class="detail-card">
                <div class="detail-card-header">
                    <span class="material-symbols-outlined"><i class="fa fa-users" aria-hidden="true"></i></span>
                    <h3>Usuarios Registrados</h3>
                </div>
                <div class="detail-card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Nombre</th>
                                    <th>Tipo Usuario</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empresa->usuarios->take(10) as $usuario)
                                <tr>
                                    <td>{{ number_format($usuario->doc_usuario, 0, '', '.') }}</td>
                                    <td>{{ $usuario->primer_nombre }} {{ $usuario->primer_apellido }}</td>
                                    <td>{{ $usuario->id_tipo_usuario->nombre_tipo ?? 'N/A' }}</td>
                                    <td>{{ $usuario->telefono ?? 'N/A' }}</td>
                                    <td>
                                        @if($usuario->estado)
                                            <span class="badge-estado {{ strtolower($usuario->estado->nombre_estado) }}">
                                                {{ $usuario->estado->nombre_estado }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($empresa->usuarios->count() > 10)
                        <p class="text-muted text-center mt-3">
                            Mostrando 10 de {{ $empresa->usuarios->count() }} usuarios
                        </p>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>

</div>

@endsection
