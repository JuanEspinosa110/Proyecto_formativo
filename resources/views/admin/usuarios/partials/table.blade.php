<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted text-uppercase small fw-bold">
                <tr>
                    <th class="ps-4 py-3">USUARIO</th>
                    <th class="py-3">Contacto</th>
                    <th class="py-3">Rol / Nivel</th>
                    <th class="py-3">Estado</th>
                    <th class="py-3 text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $u)
                    <tr class="border-top">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle overflow-hidden shadow-sm" style="width: 40px; height: 40px;">
                                    @if($u->foto_usuario)
                                        <img src="{{ asset('storage/' . $u->foto_usuario) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Foto">
                                    @else
                                        <div class="bg-primary bg-opacity-10 p-2 text-primary d-flex align-items-center justify-content-center h-100">
                                            <span class="material-symbols-rounded">person</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="fw-bold d-block text-dark">{{ $u->primer_nombre }} {{ $u->primer_apellido }}</span>
                                    <small class="text-muted">Doc: {{ $u->doc_usuario }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-medium text-dark">{{ $u->correo }}</div>
                            <div class="small text-muted">{{ $u->telefono }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 fw-semibold">
                                {{ $u->nombre_tipo ?? 'N/A' }}
                            </span>
                            @if(isset($docs_licencia[$u->doc_usuario]))
                                @php $lic = $docs_licencia[$u->doc_usuario]; @endphp
                                @if(!$lic->isVigente())
                                    <div class="mt-1">
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 d-inline-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                            <span class="material-symbols-rounded" style="font-size: 0.8rem;">error</span> Licencia Vencida
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td>
                            @php
                                $estado = $estados->firstWhere('id_estado', $u->id_estado);
                                $c = $estado && $estado->id_estado == 1 ? 'success' : 'secondary';
                            @endphp
                            <span class="badge bg-{{ $c }}-subtle text-{{ $c }} border border-{{ $c }} rounded-pill px-3">
                                {{ $estado ? $estado->nombre_estado : 'Desconocido' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-3">
                                <a href="#" 
                                   class="text-info text-decoration-none d-flex align-items-center"
                                   title="Ver detalles"
                                   data-doc="{{ $u->doc_usuario }}"
                                   data-primer-nombre="{{ $u->primer_nombre }}"
                                   data-segundo-nombre="{{ $u->segundo_nombre }}"
                                   data-primer-apellido="{{ $u->primer_apellido }}"
                                   data-segundo-apellido="{{ $u->segundo_apellido }}"
                                   data-correo="{{ $u->correo }}"
                                   data-telefono="{{ $u->telefono }}"
                                   data-rol="{{ $u->nombre_tipo }}"
                                   data-estado="{{ $u->nombre_estado }}"
                                   data-ciudad="{{ $u->nombre_city }}"
                                   data-foto="{{ $u->foto_usuario }}"
                                   data-licencia-exp="{{ isset($docs_licencia[$u->doc_usuario]) ? \Carbon\Carbon::parse($docs_licencia[$u->doc_usuario]->fecha_expedicion)->format('Y-m-d') : '' }}"
                                   data-licencia-venc="{{ isset($docs_licencia[$u->doc_usuario]) ? \Carbon\Carbon::parse($docs_licencia[$u->doc_usuario]->fecha_vencimiento)->format('Y-m-d') : '' }}"
                                   data-licencia-estado="{{ isset($docs_licencia[$u->doc_usuario]) ? $docs_licencia[$u->doc_usuario]->estado_expiracion : '' }}"
                                   data-licencia-archivo="{{ isset($docs_licencia[$u->doc_usuario]) ? $docs_licencia[$u->doc_usuario]->archivo : '' }}"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modalVerUsuario">
                                    <span class="material-symbols-rounded fs-5">visibility</span>
                                </a>
                                <a href="#" 
                                   class="text-primary text-decoration-none d-flex align-items-center"
                                   title="Editar usuario"
                                   data-doc="{{ $u->doc_usuario }}"
                                   data-primer-nombre="{{ $u->primer_nombre }}"
                                   data-segundo-nombre="{{ $u->segundo_nombre }}"
                                   data-primer-apellido="{{ $u->primer_apellido }}"
                                   data-segundo-apellido="{{ $u->segundo_apellido }}"
                                   data-correo="{{ $u->correo }}"
                                   data-telefono="{{ $u->telefono }}"
                                   data-rol="{{ $u->id_tipo_usuario }}"
                                   data-estado_id="{{ $u->id_estado }}"
                                   data-foto="{{ $u->foto_usuario }}"
                                   data-fecha-nacimiento="{{ $u->fecha_nacimiento }}"
                                   data-licencia-exp="{{ isset($docs_licencia[$u->doc_usuario]) ? \Carbon\Carbon::parse($docs_licencia[$u->doc_usuario]->fecha_expedicion)->format('Y-m-d') : '' }}"
                                   data-licencia-venc="{{ isset($docs_licencia[$u->doc_usuario]) ? \Carbon\Carbon::parse($docs_licencia[$u->doc_usuario]->fecha_vencimiento)->format('Y-m-d') : '' }}"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modalEditarUsuario">
                                    <span class="material-symbols-rounded fs-5">edit</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-2">{{ $usuarios->links() }}</div>
