@extends('fabricasoft::layouts.app')
@section('title', 'Todas las Solicitudes - FABRICASOFT')
@section('content')

<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-list me-2"></i>Todas las Solicitudes
        </h1>
                 <div>
             <a href="{{ route('fabricasoft.admin.dashboard') }}" class="btn btn-sena-outline me-2">
                 <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
             </a>
         </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('fabricasoft.admin.filtrar.solicitudes') }}" method="GET">
                <div class="row">
                    <!-- Búsqueda de texto -->
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Nombre, email, organización..." value="{{ request('search') }}">
                    </div>
                    
                                         <!-- Tipo de software -->
                     <div class="col-md-4 mb-3">
                         <label for="software_type" class="form-label">Tipo de Software</label>
                         <select class="form-select" id="software_type" name="software_type">
                             <option value="">Todos los tipos</option>
                             <option value="Sistema Web" {{ request('software_type') == 'Sistema Web' ? 'selected' : '' }}>Sistema Web</option>
                             <option value="Aplicación Móvil" {{ request('software_type') == 'Aplicación Móvil' ? 'selected' : '' }}>Aplicación Móvil</option>
                             <option value="Sistema de Escritorio" {{ request('software_type') == 'Sistema de Escritorio' ? 'selected' : '' }}>Sistema de Escritorio</option>
                             <option value="E-commerce" {{ request('software_type') == 'E-commerce' ? 'selected' : '' }}>E-commerce</option>
                             <option value="CRM" {{ request('software_type') == 'CRM' ? 'selected' : '' }}>CRM</option>
                             <option value="ERP" {{ request('software_type') == 'ERP' ? 'selected' : '' }}>ERP</option>
                             <option value="Otro" {{ request('software_type') == 'Otro' ? 'selected' : '' }}>Otro</option>
                         </select>
                     </div>
                     
                     <!-- Fecha de creación -->
                     <div class="col-md-4 mb-3">
                         <label for="created_date" class="form-label">Fecha de Creación</label>
                         <input type="date" class="form-control" id="created_date" name="created_date" value="{{ request('created_date') }}">
                     </div>
                </div>
                
                <div class="row">
                    <!-- Botones de acción -->
                    <div class="col-md-12 mb-3 d-flex justify-content-end">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sena">
                                <i class="fas fa-search me-2"></i>Filtrar
                            </button>
                            <a href="{{ route('fabricasoft.admin.solicitudes') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Solicitudes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Lista de Solicitudes
                <span class="badge bg-primary ms-2">{{ $solicitudes->total() }}</span>
            </h6>
        </div>
        <div class="card-body">
            @if($solicitudes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Información del Cliente</th>
                                <th>Proyecto</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudes as $solicitud)
                            <tr>
                                <td>
                                    <strong>#{{ $solicitud->id }}</strong>
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <strong>{{ $solicitud->full_name }}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <i class="fas fa-envelope me-1 text-muted"></i>
                                        <a href="mailto:{{ $solicitud->email }}" class="text-decoration-none">
                                            {{ $solicitud->email }}
                                        </a>
                                    </div>
                                    @if($solicitud->phone)
                                    <div class="mb-1">
                                        <i class="fas fa-phone me-1 text-muted"></i>
                                        <a href="tel:{{ $solicitud->phone }}" class="text-decoration-none">
                                            {{ $solicitud->phone }}
                                        </a>
                                    </div>
                                    @endif
                                    <div>
                                        <i class="fas fa-building me-1 text-muted"></i>
                                        <strong>{{ $solicitud->organization }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <span class="badge bg-info">{{ $solicitud->software_type }}</span>
                                    </div>
                                    <div class="small text-muted">
                                        {{ Str::limit($solicitud->project_description, 100) }}
                                    </div>
                                    @if($solicitud->additional_requirements)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Requisitos:</strong> {{ Str::limit($solicitud->additional_requirements, 80) }}
                                        </small>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @if($solicitud->status == 'pending')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif($solicitud->status == 'approved')
                                        <span class="badge bg-success">Aprobada</span>
                                    @else
                                        <span class="badge bg-danger">Rechazada</span>
                                    @endif
                                    
                                    @if($solicitud->reviewed_at)
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            Revisada: {{ $solicitud->reviewed_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <strong>Creada:</strong><br>
                                        <small>{{ $solicitud->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if($solicitud->updated_at != $solicitud->created_at)
                                    <div>
                                        <strong>Actualizada:</strong><br>
                                        <small>{{ $solicitud->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('fabricasoft.admin.show.solicitud', $solicitud->id) }}" 
                                           class="btn btn-sm btn-outline-primary mb-1" title="Ver Detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        
                                        @if($solicitud->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-outline-success mb-1" 
                                                    onclick="aprobarSolicitud({{ $solicitud->id }})" title="Aprobar">
                                                <i class="fas fa-check"></i> Aprobar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger mb-1" 
                                                    onclick="rechazarSolicitud({{ $solicitud->id }})" title="Rechazar">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        @endif
                                        
                                        @if($solicitud->admin_notes)
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="verNotas({{ $solicitud->id }}, '{{ addslashes($solicitud->admin_notes) }}')" title="Ver Notas">
                                            <i class="fas fa-sticky-note"></i> Notas
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $solicitudes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-500">No se encontraron solicitudes</h4>
                    <p class="text-gray-400">No hay solicitudes que coincidan con los filtros aplicados.</p>
                    <a href="{{ route('fabricasoft.admin.solicitudes') }}" class="btn btn-sena">
                        <i class="fas fa-times me-2"></i>Limpiar Filtros
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Aprobar Solicitud -->
<div class="modal fade" id="modalAprobar" tabindex="-1" aria-labelledby="modalAprobarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAprobarLabel">Aprobar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAprobar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Notas del Administrador (Opcional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                  placeholder="Agrega notas adicionales sobre la aprobación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Aprobar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Rechazar Solicitud -->
<div class="modal fade" id="modalRechazar" tabindex="-1" aria-labelledby="modalRechazarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRechazarLabel">Rechazar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_notes_reject" class="form-label">Motivo del Rechazo *</label>
                        <textarea class="form-control" id="admin_notes_reject" name="admin_notes" rows="3" 
                                  placeholder="Explica el motivo del rechazo..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Rechazar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Notas -->
<div class="modal fade" id="modalNotas" tabindex="-1" aria-labelledby="modalNotasLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotasLabel">Notas del Administrador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="notasContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function aprobarSolicitud(id) {
    const form = document.getElementById('formAprobar');
    form.action = `/fabricasoft/admin/solicitud/${id}/aprobar`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalAprobar'));
    modal.show();
}

function rechazarSolicitud(id) {
    const form = document.getElementById('formRechazar');
    form.action = `/fabricasoft/admin/solicitud/${id}/rechazar`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalRechazar'));
    modal.show();
}

function verNotas(id, notas) {
    document.getElementById('notasContent').innerHTML = `<p>${notas}</p>`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalNotas'));
    modal.show();
}
</script>
@endpush
