@extends('fabricasoft::layouts.app')
@section('title', 'Detalles de Solicitud #' . $solicitud->id . ' - FABRICASOFT')
@section('content')

<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye me-2"></i>Detalles de Solicitud #{{ $solicitud->id }}
        </h1>
        <div>
            <a href="{{ route('fabricasoft.admin.dashboard') }}" class="btn btn-sena-outline me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
            @if($solicitud->status == 'pending')
                <button type="button" class="btn btn-success me-2" onclick="aprobarSolicitud({{ $solicitud->id }})">
                    <i class="fas fa-check me-2"></i>Aprobar
                </button>
                <button type="button" class="btn btn-danger" onclick="rechazarSolicitud({{ $solicitud->id }})">
                    <i class="fas fa-times me-2"></i>Rechazar
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Información del Cliente -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Información del Cliente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <p class="form-control-plaintext">{{ $solicitud->full_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <p class="form-control-plaintext">
                                <a href="mailto:{{ $solicitud->email }}" class="text-decoration-none">
                                    {{ $solicitud->email }}
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <p class="form-control-plaintext">
                                @if($solicitud->phone)
                                    <a href="tel:{{ $solicitud->phone }}" class="text-decoration-none">
                                        {{ $solicitud->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Empresa/Institución</label>
                            <p class="form-control-plaintext">{{ $solicitud->organization }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Proyecto -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-project-diagram me-2"></i>Detalles del Proyecto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Software</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info fs-6">{{ $solicitud->software_type }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado Actual</label>
                            <p class="form-control-plaintext">
                                @if($solicitud->status == 'pending')
                                    <span class="badge bg-warning fs-6">Pendiente</span>
                                @elseif($solicitud->status == 'approved')
                                    <span class="badge bg-success fs-6">Aprobada</span>
                                @else
                                    <span class="badge bg-danger fs-6">Rechazada</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción del Proyecto</label>
                        <div class="form-control-plaintext" style="min-height: 100px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                            {{ $solicitud->project_description }}
                        </div>
                    </div>
                    
                    @if($solicitud->additional_requirements)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Requisitos Adicionales</label>
                        <div class="form-control-plaintext" style="min-height: 80px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                            {{ $solicitud->additional_requirements }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Historial de Cambios -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Historial de Cambios
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Solicitud Creada</h6>
                                <p class="timeline-text">{{ $solicitud->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        
                        @if($solicitud->updated_at != $solicitud->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Última Actualización</h6>
                                <p class="timeline-text">{{ $solicitud->updated_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($solicitud->reviewed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $solicitud->status == 'approved' ? 'bg-success' : 'bg-danger' }}">
                                <i class="fas {{ $solicitud->status == 'approved' ? 'fa-check' : 'fa-times' }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">
                                    Solicitud {{ $solicitud->status == 'approved' ? 'Aprobada' : 'Rechazada' }}
                                </h6>
                                <p class="timeline-text">{{ $solicitud->reviewed_at->format('d/m/Y H:i:s') }}</p>
                                @if($solicitud->reviewer)
                                <p class="timeline-text">
                                    <small class="text-muted">Revisada por: {{ $solicitud->reviewer->name }}</small>
                                </p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar con Información Adicional -->
        <div class="col-lg-4">
            <!-- Estado y Acciones -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Estado y Acciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado del Flujo</label>
                        <div class="d-grid">
                            <span class="badge bg-secondary fs-5 py-2">{{ $solicitud->workflow_status_text }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado de Análisis</label>
                        <div class="d-grid">
                            <span class="badge bg-secondary fs-6 py-2">{{ $solicitud->analysis_status_text }}</span>
                        </div>
                    </div>
                    
                    @if($solicitud->workflow_status == 'pending')
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-info" onclick="asignarAnalista()">
                            <i class="fas fa-user-plus me-2"></i>Asignar Analista
                        </button>
                    </div>
                    @endif
                    
                    @if($solicitud->workflow_status == 'assigned' || $solicitud->workflow_status == 'analysis')
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-warning" onclick="cambiarAnalista()">
                            <i class="fas fa-user-edit me-2"></i>Cambiar Analista
                        </button>
                    </div>
                    @endif
                    
                    @if($solicitud->status == 'pending')
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" onclick="aprobarSolicitud({{ $solicitud->id }})">
                            <i class="fas fa-check me-2"></i>Aprobar Solicitud
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rechazarSolicitud({{ $solicitud->id }})">
                            <i class="fas fa-times me-2"></i>Rechazar Solicitud
                        </button>
                    </div>
                    @endif
                    
                    @if($solicitud->status == 'approved')
                    <div class="d-grid gap-2">
                        @if($existingProject)
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Ya existe un equipo Scrum para esta solicitud.</strong><br>
                                <small>Proyecto: {{ $existingProject->project_name }}</small>
                            </div>
                            <button type="button" class="btn btn-secondary" disabled>
                                <i class="fas fa-project-diagram me-2"></i>Proyecto Ya Creado
                            </button>
                        @else
                            <button type="button" class="btn btn-primary" onclick="mostrarModalCrearProyecto()">
                                <i class="fas fa-project-diagram me-2"></i>Crear Equipo Scrum
                            </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Información del Analista -->
            @if($solicitud->assigned_analyst_id)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-tie me-2"></i>Analista Asignado
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Analista:</small><br>
                        <strong>{{ $solicitud->analyst ? $solicitud->analyst->name : 'Usuario no encontrado' }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Email:</small><br>
                        <strong>{{ $solicitud->analyst ? $solicitud->analyst->email : 'N/A' }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Asignada:</small><br>
                        <strong>{{ $solicitud->assigned_at->format('d/m/Y H:i:s') }}</strong>
                    </div>
                </div>
            </div>
            @endif

            <!-- Información del SRS -->
            @if($solicitud->srs_file_path)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>Documento SRS
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">SRS Subido:</small><br>
                        <strong>{{ $solicitud->srs_uploaded_at->format('d/m/Y H:i:s') }}</strong>
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('fabricasoft.admin.descargar.srs', $solicitud->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>Descargar SRS
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Información de Contacto -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-address-book me-2"></i>Contacto Rápido
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $solicitud->email }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Enviar Email
                        </a>
                        @if($solicitud->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $solicitud->phone) }}" target="_blank" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                        @endif
                        <button type="button" class="btn btn-outline-info" onclick="copiarInformacion()">
                            <i class="fas fa-copy me-2"></i>Copiar Información
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notas del Administrador -->
            @if($solicitud->admin_notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sticky-note me-2"></i>Notas del Administrador
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        {{ $solicitud->admin_notes }}
                    </div>
                </div>
            </div>
            @endif

            <!-- Información Técnica -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información Técnica
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">ID de Solicitud:</small><br>
                        <strong>{{ $solicitud->id }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Tipo de Cliente:</small><br>
                        <span class="badge bg-secondary">{{ $solicitud->client_type == 'cliente_externo' ? 'Cliente Externo' : 'Cliente Interno' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Fecha de Creación:</small><br>
                        <strong>{{ $solicitud->created_at->format('d/m/Y H:i:s') }}</strong>
                    </div>
                    @if($solicitud->reviewed_at)
                    <div class="mb-2">
                        <small class="text-muted">Fecha de Revisión:</small><br>
                        <strong>{{ $solicitud->reviewed_at->format('d/m/Y H:i:s') }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Aprobar Solicitud -->
<div class="modal fade" id="modalAprobar" tabindex="-1" aria-labelledby="modalAprobarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAprobarLabel">Aprobar Solicitud #{{ $solicitud->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAprobar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Estás a punto de aprobar la solicitud de <strong>{{ $solicitud->full_name }}</strong> 
                        para el desarrollo de un <strong>{{ $solicitud->software_type }}</strong>.
                    </div>
                    
                    @if($solicitud->client_type === 'cliente_externo')
                        <div class="alert alert-success">
                            <i class="fas fa-user-plus me-2"></i>
                            <strong>Cliente Externo:</strong> Al aprobar esta solicitud, se creará automáticamente 
                            una cuenta de usuario para que el cliente pueda acceder al sistema y hacer seguimiento de su proyecto.
                        </div>
                    @endif
                    
                    <div class="form-group">
                        <label for="admin_notes" class="form-label">Notas del Administrador (Opcional)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                  placeholder="Agrega comentarios o notas sobre la aprobación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Confirmar Aprobación
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
                <h5 class="modal-title" id="modalRechazarLabel">Rechazar Solicitud #{{ $solicitud->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRechazar" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Estás a punto de rechazar la solicitud de <strong>{{ $solicitud->full_name }}</strong>.
                        Esta acción requiere un motivo obligatorio.
                    </div>
                    <div class="mb-3">
                        <label for="admin_notes_reject" class="form-label">Motivo del Rechazo *</label>
                        <textarea class="form-control" id="admin_notes_reject" name="admin_notes" rows="4" 
                                  placeholder="Explica detalladamente el motivo del rechazo. Esta información será importante para el cliente..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Asignar Analista -->
<div class="modal fade" id="modalAsignarAnalista" tabindex="-1" aria-labelledby="modalAsignarAnalistaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAsignarAnalistaLabel">Asignar Analista a Solicitud #{{ $solicitud->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('fabricasoft.admin.asignar.analista', $solicitud->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Selecciona un analista para revisar la solicitud de <strong>{{ $solicitud->full_name }}</strong>.
                    </div>
                    <div class="mb-3">
                        <label for="analyst_id" class="form-label">Analista *</label>
                        <select class="form-select" id="analyst_id" name="analyst_id" required>
                            <option value="">Selecciona un analista</option>
                            @foreach($analysts as $analyst)
                                <option value="{{ $analyst->id }}">{{ $analyst->name }} ({{ $analyst->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="admin_notes_assign" class="form-label">Notas del Administrador (Opcional)</label>
                        <textarea class="form-control" id="admin_notes_assign" name="admin_notes" rows="3" 
                                  placeholder="Agrega notas sobre la asignación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-user-plus me-2"></i>Asignar Analista
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Cambiar Analista -->
<div class="modal fade" id="modalCambiarAnalista" tabindex="-1" aria-labelledby="modalCambiarAnalistaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCambiarAnalistaLabel">Cambiar Analista de Solicitud #{{ $solicitud->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('fabricasoft.admin.cambiar.analista', $solicitud->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Analista Actual:</strong> {{ $solicitud->analyst ? $solicitud->analyst->name : 'No asignado' }}
                    </div>
                    <div class="mb-3">
                        <label for="new_analyst_id" class="form-label">Nuevo Analista *</label>
                        <select class="form-select" id="new_analyst_id" name="new_analyst_id" required>
                            <option value="">Selecciona un nuevo analista</option>
                            @foreach($analysts as $analyst)
                                @if($analyst->id != $solicitud->assigned_analyst_id)
                                    <option value="{{ $analyst->id }}">{{ $analyst->name }} ({{ $analyst->email }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="admin_notes_change" class="form-label">Notas del Administrador (Opcional)</label>
                        <textarea class="form-control" id="admin_notes_change" name="admin_notes" rows="3" 
                                  placeholder="Explica el motivo del cambio de analista..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-user-edit me-2"></i>Cambiar Analista
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Botones de prueba temporal -->
<div class="text-center mb-3">
    <button type="button" class="btn btn-warning btn-sm me-2" onclick="testModal()">
        <i class="fas fa-bug me-2"></i>Test Modal
    </button>
    <button type="button" class="btn btn-info btn-sm me-2" onclick="testFormSubmit()">
        <i class="fas fa-paper-plane me-2"></i>Test Form Submit
    </button>
    <button type="button" class="btn btn-success btn-sm me-2" onclick="checkSolicitudStatus()">
        <i class="fas fa-info-circle me-2"></i>Check Status
    </button>
    <button type="button" class="btn btn-primary btn-sm me-2" onclick="openModalManually()">
        <i class="fas fa-external-link-alt me-2"></i>Open Modal
    </button>
    <button type="button" class="btn btn-danger btn-sm me-2" onclick="submitFormManually()">
        <i class="fas fa-rocket me-2"></i>Submit Form
    </button>
    <button type="button" class="btn btn-dark btn-sm me-2" onclick="testOriginalButton()">
        <i class="fas fa-play me-2"></i>Test Original
    </button>
    <button type="button" class="btn btn-light btn-sm" onclick="cleanupAndRestore()">
        <i class="fas fa-broom me-2"></i>Cleanup
    </button>
</div>

<!-- Modal para Crear Proyecto Scrum -->
<div class="modal fade" id="modalCrearProyecto" tabindex="-1" aria-labelledby="modalCrearProyectoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearProyectoLabel">Crear Equipo Scrum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('fabricasoft.admin.crear.proyecto', $solicitud->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Crear un nuevo equipo Scrum para esta solicitud aprobada. Se crearán automáticamente las fases del proyecto.
                    </div>
                    
                    <div class="mb-3">
                        <label for="project_name" class="form-label">Nombre del Proyecto *</label>
                        <input type="text" class="form-control" id="project_name" name="project_name" 
                               value="{{ $solicitud->software_type }} - {{ $solicitud->full_name }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción del Proyecto *</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4" placeholder="Describe el proyecto en detalle..." required>{{ $solicitud->project_description }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scrum_master_id" class="form-label">Scrum Master (Analista) *</label>
                                <select class="form-select" id="scrum_master_id" name="scrum_master_id" required>
                                    <option value="">Selecciona un analista</option>
                                    @foreach($analysts as $analyst)
                                        <option value="{{ $analyst->id }}" {{ $solicitud->assigned_analyst_id == $analyst->id ? 'selected' : '' }}>
                                            {{ $analyst->nickname ?? $analyst->name }} ({{ $analyst->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estimated_end_date" class="form-label">Fecha de Finalización Estimada</label>
                        <input type="date" class="form-control" id="estimated_end_date" name="estimated_end_date">
                    </div>
                    
                    <div class="mb-3">
                        <label for="project_goals" class="form-label">Metas del Proyecto</label>
                        <textarea class="form-control" id="project_goals" name="project_goals" 
                                  rows="3" placeholder="Define las metas principales del proyecto..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="success_criteria" class="form-label">Criterios de Éxito</label>
                        <textarea class="form-control" id="success_criteria" name="success_criteria" 
                                  rows="3" placeholder="Define los criterios para considerar el proyecto exitoso..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-project-diagram me-2"></i>Crear Equipo Scrum
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-weight: 600;
    color: #495057;
}

.timeline-text {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}
</style>
@endpush

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

function asignarAnalista() {
    console.log('=== DEBUG: Función asignarAnalista() ejecutada ===');
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('ERROR: Bootstrap no está disponible');
        alert('Error: Bootstrap no está disponible. Recarga la página.');
        return;
    }
    
    console.log('Bootstrap disponible:', bootstrap);
    
    // Buscar el modal
    const modalElement = document.getElementById('modalAsignarAnalista');
    console.log('Modal element encontrado:', modalElement);
    
    if (!modalElement) {
        console.error('ERROR: Modal modalAsignarAnalista no encontrado en el DOM');
        alert('Error: Modal no encontrado. Verifica que el HTML esté correcto.');
        return;
    }
    
    try {
        // Crear instancia del modal
        const modal = new bootstrap.Modal(modalElement);
        console.log('Modal Bootstrap creado:', modal);
        
        // Mostrar el modal
        modal.show();
        console.log('Modal mostrado exitosamente');
        
    } catch (error) {
        console.error('ERROR al crear/mostrar modal:', error);
        alert('Error al mostrar el modal: ' + error.message);
    }
}

function cambiarAnalista() {
    const modal = new bootstrap.Modal(document.getElementById('modalCambiarAnalista'));
    modal.show();
}

function copiarInformacion() {
    const info = `Cliente: ${document.querySelector('.form-control-plaintext').textContent}
Email: {{ $solicitud->email }}
Teléfono: {{ $solicitud->phone ?? 'No especificado' }}
Empresa: {{ $solicitud->organization }}
Proyecto: {{ $solicitud->software_type }}
ID Solicitud: {{ $solicitud->id }}`;
    
    navigator.clipboard.writeText(info).then(() => {
        // Mostrar notificación de éxito
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Copiado!';
        btn.classList.remove('btn-outline-info');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-info');
        }, 2000);
    });
}

function checkSolicitudStatus() {
    console.log('=== TEST: Función checkSolicitudStatus() ejecutada ===');
    
    // Verificar datos de la solicitud desde la vista
    const solicitudId = {{ $solicitud->id }};
    const solicitudStatus = '{{ $solicitud->status }}';
    const assignedAnalystId = {{ $solicitud->assigned_analyst_id ?? 'null' }};
    
    console.log('TEST: Solicitud ID:', solicitudId);
    console.log('TEST: Solicitud Status:', solicitudStatus);
    console.log('TEST: Assigned Analyst ID:', assignedAnalystId);
    
    // Verificar si hay proyecto existente
    @if($existingProject)
        console.log('TEST: Ya existe proyecto:', '{{ $existingProject->project_name }}');
    @else
        console.log('TEST: No existe proyecto previo');
    @endif
    
    // Verificar analistas disponibles
    const analystsCount = {{ $analysts->count() }};
    console.log('TEST: Analistas disponibles:', analystsCount);
    
    if (analystsCount === 0) {
        console.error('TEST ERROR: No hay analistas disponibles');
        alert('TEST ERROR: No hay analistas disponibles');
        return;
    }
    
    // Verificar que la solicitud esté aprobada
    if (solicitudStatus !== 'approved') {
        console.error('TEST ERROR: La solicitud no está aprobada. Status:', solicitudStatus);
        alert('TEST ERROR: La solicitud no está aprobada. Status: ' + solicitudStatus);
        return;
    }
    
    // Verificar que tenga analista asignado
    if (!assignedAnalystId) {
        console.error('TEST ERROR: La solicitud no tiene analista asignado');
        alert('TEST ERROR: La solicitud no tiene analista asignado');
        return;
    }
    
    // Verificar ruta del formulario
    const form = document.querySelector('#modalCrearProyecto form');
    if (form) {
        console.log('TEST: Ruta del formulario:', form.action);
        console.log('TEST: Método del formulario:', form.method);
        
        // Verificar que la ruta sea correcta
        const expectedRoute = '/fabricasoft/admin/solicitud/' + solicitudId + '/crear-proyecto';
        if (form.action.includes(expectedRoute)) {
            console.log('TEST: Ruta del formulario es correcta');
        } else {
            console.error('TEST ERROR: Ruta del formulario incorrecta');
            console.log('TEST: Esperada:', expectedRoute);
            console.log('TEST: Actual:', form.action);
        }
    }
    
    console.log('TEST: Solicitud válida para crear proyecto');
    
    // Verificar campos del formulario
    if (form) {
        const requiredFields = ['project_name', 'description', 'scrum_master_id'];
        const missingFields = [];
        
        requiredFields.forEach(field => {
            const element = form.querySelector(`[name="${field}"]`);
            if (!element || !element.value || element.value.trim() === '') {
                missingFields.push(field);
            }
        });
        
        if (missingFields.length > 0) {
            console.error('TEST ERROR: Campos requeridos faltantes:', missingFields);
            alert('TEST ERROR: Campos requeridos faltantes: ' + missingFields.join(', '));
            return;
        }
        
        console.log('TEST: Todos los campos requeridos están presentes');
    }
    
    alert('TEST: Solicitud válida para crear proyecto');
}

function openModalManually() {
    console.log('=== TEST: Función openModalManually() ejecutada ===');
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('TEST ERROR: Bootstrap no está disponible');
        alert('TEST ERROR: Bootstrap no está disponible');
        return;
    }
    
    // Buscar el modal
    const modalElement = document.getElementById('modalCrearProyecto');
    if (!modalElement) {
        console.error('TEST ERROR: Modal no encontrado');
        alert('TEST ERROR: Modal no encontrado');
        return;
    }
    
    try {
        // Crear instancia del modal
        const modal = new bootstrap.Modal(modalElement);
        
        // Mostrar el modal
        modal.show();
        console.log('TEST: Modal abierto manualmente exitosamente');
        
        // Verificar que el modal esté visible
        setTimeout(() => {
            const isVisible = modalElement.classList.contains('show');
            console.log('TEST: Modal visible:', isVisible);
            
            if (!isVisible) {
                console.error('TEST ERROR: Modal no se mostró correctamente');
                alert('TEST ERROR: Modal no se mostró correctamente');
            } else {
                console.log('TEST: Modal funcionando correctamente');
                alert('TEST: Modal funcionando correctamente');
            }
        }, 100);
        
    } catch (error) {
        console.error('TEST ERROR al abrir modal:', error);
        alert('TEST ERROR: ' + error.message);
    }
}

function submitFormManually() {
    console.log('=== TEST: Función submitFormManually() ejecutada ===');
    
    const form = document.querySelector('#modalCrearProyecto form');
    if (!form) {
        console.error('TEST ERROR: Formulario no encontrado');
        alert('TEST ERROR: Formulario no encontrado');
        return;
    }
    
    console.log('TEST: Formulario encontrado, enviando manualmente...');
    
    // Verificar campos requeridos antes de enviar
    const requiredFields = ['project_name', 'description', 'scrum_master_id'];
    const missingFields = [];
    
    requiredFields.forEach(field => {
        const element = form.querySelector(`[name="${field}"]`);
        if (!element || !element.value || element.value.trim() === '') {
            missingFields.push(field);
        }
    });
    
    if (missingFields.length > 0) {
        console.error('TEST ERROR: Campos requeridos faltantes:', missingFields);
        alert('TEST ERROR: Campos requeridos faltantes: ' + missingFields.join(', '));
        return;
    }
    
    console.log('TEST: Todos los campos requeridos están presentes, enviando...');
    
    // Crear FormData
    const formData = new FormData(form);
    
    // Log de todos los campos
    console.log('TEST: Campos del formulario:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }
    
    // Enviar formulario
    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('TEST: Response status:', response.status);
        console.log('TEST: Response headers:', response.headers);
        
        if (response.redirected) {
            console.log('TEST: Redirección detectada a:', response.url);
            window.location.href = response.url;
            return;
        }
        
        return response.text();
    })
    .then(data => {
        if (data) {
            console.log('TEST: Response data:', data);
            
            // Verificar si hay error en la respuesta
            if (data.includes('error') || data.includes('Error')) {
                console.error('TEST ERROR: Respuesta contiene error');
                alert('TEST ERROR: Respuesta contiene error. Revisa la consola.');
            } else {
                console.log('TEST: Respuesta exitosa');
                alert('TEST: Formulario enviado exitosamente. Revisa la consola para detalles.');
            }
        }
    })
    .catch(error => {
        console.error('TEST ERROR en fetch:', error);
        alert('TEST ERROR: ' + error.message);
    });
}

// Función para verificar que el botón original funcione
function testOriginalButton() {
    console.log('=== TEST: Verificando botón original ===');
    
    // Simular clic en el botón original
    const originalButton = document.querySelector('button[onclick="mostrarModalCrearProyecto()"]');
    if (originalButton) {
        console.log('TEST: Botón original encontrado, simulando clic...');
        originalButton.click();
    } else {
        console.error('TEST ERROR: Botón original no encontrado');
        alert('TEST ERROR: Botón original no encontrado');
    }
}

// Función para limpiar logs y restaurar funcionalidad
function cleanupAndRestore() {
    console.log('=== CLEANUP: Limpiando logs y restaurando funcionalidad ===');
    
    // Limpiar console
    console.clear();
    
    // Restaurar función original
    window.mostrarModalCrearProyecto = function() {
        console.log('Modal abierto correctamente');
        const modal = new bootstrap.Modal(document.getElementById('modalCrearProyecto'));
        modal.show();
    };
    
    // Ocultar botones de prueba
    const testButtons = document.querySelector('.text-center.mb-3');
    if (testButtons) {
        testButtons.style.display = 'none';
    }
    
    console.log('CLEANUP: Funcionalidad restaurada, botones de prueba ocultos');
    alert('CLEANUP: Funcionalidad restaurada. El botón "Crear Equipo Scrum" debería funcionar ahora.');
}

// Función final para verificar que todo funcione
function finalTest() {
    console.log('=== FINAL TEST: Verificando funcionalidad completa ===');
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('FINAL TEST ERROR: Bootstrap no disponible');
        return false;
    }
    
    // Verificar que el modal exista
    const modal = document.getElementById('modalCrearProyecto');
    if (!modal) {
        console.error('FINAL TEST ERROR: Modal no encontrado');
        return false;
    }
    
    // Verificar que el formulario exista
    const form = modal.querySelector('form');
    if (!form) {
        console.error('FINAL TEST ERROR: Formulario no encontrado');
        return false;
    }
    
    // Verificar que la ruta sea correcta
    const expectedRoute = '/fabricasoft/admin/solicitud/{{ $solicitud->id }}/crear-proyecto';
    if (!form.action.includes(expectedRoute)) {
        console.error('FINAL TEST ERROR: Ruta incorrecta');
        return false;
    }
    
    // Verificar campos requeridos
    const requiredFields = ['project_name', 'description', 'scrum_master_id'];
    for (let field of requiredFields) {
        const element = form.querySelector(`[name="${field}"]`);
        if (!element) {
            console.error(`FINAL TEST ERROR: Campo ${field} no encontrado`);
            return false;
        }
    }
    
    console.log('FINAL TEST: Todo está funcionando correctamente');
    return true;
}

function testFormSubmit() {
    console.log('=== TEST: Función testFormSubmit() ejecutada ===');
    
    const form = document.querySelector('#modalCrearProyecto form');
    if (!form) {
        console.error('TEST ERROR: Formulario no encontrado');
        alert('TEST ERROR: Formulario no encontrado');
        return;
    }
    
    console.log('TEST: Formulario encontrado, simulando envío...');
    
    // Crear FormData
    const formData = new FormData(form);
    console.log('TEST: FormData creado');
    
    // Log de todos los campos
    for (let [key, value] of formData.entries()) {
        console.log(`TEST: ${key} = ${value}`);
    }
    
    // Simular envío con fetch
    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('TEST: Response status:', response.status);
        console.log('TEST: Response headers:', response.headers);
        return response.text();
    })
    .then(data => {
        console.log('TEST: Response data:', data);
        alert('TEST: Formulario enviado. Revisa la consola para detalles.');
    })
    .catch(error => {
        console.error('TEST ERROR en fetch:', error);
        alert('TEST ERROR: ' + error.message);
    });
}

function testModal() {
    console.log('=== TEST: Función testModal() ejecutada ===');
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('TEST ERROR: Bootstrap no está disponible');
        alert('TEST ERROR: Bootstrap no está disponible');
        return;
    }
    
    console.log('TEST: Bootstrap disponible');
    
    // Buscar el modal
    const modalElement = document.getElementById('modalCrearProyecto');
    console.log('TEST: Modal element encontrado:', modalElement);
    
    if (!modalElement) {
        console.error('TEST ERROR: Modal no encontrado');
        alert('TEST ERROR: Modal no encontrado');
        return;
    }
    
    // Verificar elementos del formulario
    const form = modalElement.querySelector('form');
    console.log('TEST: Formulario encontrado:', form);
    
    if (form) {
        console.log('TEST: Action del formulario:', form.action);
        console.log('TEST: Method del formulario:', form.method);
        
        const requiredFields = ['project_name', 'description', 'scrum_master_id'];
        requiredFields.forEach(field => {
            const element = form.querySelector(`[name="${field}"]`);
            console.log(`TEST: Campo ${field}:`, element);
            if (element) {
                console.log(`TEST: Valor de ${field}:`, element.value);
            }
        });
        
        // Verificar CSRF token
        const csrfToken = form.querySelector('input[name="_token"]');
        console.log('TEST: CSRF token encontrado:', csrfToken ? 'SÍ' : 'NO');
    }
    
    try {
        // Crear instancia del modal
        const modal = new bootstrap.Modal(modalElement);
        console.log('TEST: Modal Bootstrap creado:', modal);
        
        // Mostrar el modal
        modal.show();
        console.log('TEST: Modal mostrado exitosamente');
        
    } catch (error) {
        console.error('TEST ERROR al mostrar modal:', error);
        alert('TEST ERROR: ' + error.message);
    }
}

function mostrarModalCrearProyecto() {
    console.log('=== DEBUG: Función mostrarModalCrearProyecto() ejecutada ===');
    
    // Verificar si ya existe un proyecto para esta solicitud
    @if($existingProject)
        console.log('Ya existe proyecto:', '{{ $existingProject->project_name }}');
        alert('⚠️ Ya existe un equipo Scrum para esta solicitud.\n\nProyecto: {{ $existingProject->project_name }}\n\nNo se pueden crear proyectos duplicados.');
        return;
    @endif
    
    console.log('No existe proyecto previo, procediendo...');
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('ERROR: Bootstrap no está disponible');
        alert('Error: Bootstrap no está disponible. Recarga la página.');
        return;
    }
    
    console.log('Bootstrap disponible:', bootstrap);
    
    // Buscar el modal
    const modalElement = document.getElementById('modalCrearProyecto');
    console.log('Modal element encontrado:', modalElement);
    
    if (!modalElement) {
        console.error('ERROR: Modal modalCrearProyecto no encontrado en el DOM');
        alert('Error: Modal no encontrado. Verifica que el HTML esté correcto.');
        return;
    }
    
    try {
        // Crear instancia del modal
        const modal = new bootstrap.Modal(modalElement);
        console.log('Modal Bootstrap creado:', modal);
        
        // Mostrar el modal
        modal.show();
        console.log('Modal mostrado exitosamente');
        
    } catch (error) {
        console.error('ERROR al crear/mostrar modal:', error);
        alert('Error al mostrar el modal: ' + error.message);
    }
}

// Función para debuggear el envío del formulario
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DEBUG: DOM cargado, configurando formulario ===');
    
    const formCrearProyecto = document.querySelector('#modalCrearProyecto form');
    console.log('Formulario encontrado:', formCrearProyecto);
    
    if (formCrearProyecto) {
        console.log('Configurando event listener para submit');
        
        // Verificar que la ruta sea correcta
        const expectedRoute = '/fabricasoft/admin/solicitud/{{ $solicitud->id }}/crear-proyecto';
        if (!formCrearProyecto.action.includes(expectedRoute)) {
            console.error('ERROR: Ruta del formulario incorrecta');
            console.log('Esperada:', expectedRoute);
            console.log('Actual:', formCrearProyecto.action);
        } else {
            console.log('Ruta del formulario correcta');
        }
        
        formCrearProyecto.addEventListener('submit', function(e) {
            console.log('=== DEBUG: Formulario enviado ===');
            console.log('Action:', this.action);
            console.log('Method:', this.method);
            
            // Log de todos los campos del formulario
            const formData = new FormData(this);
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            // Verificar campos requeridos
            const requiredFields = ['project_name', 'description', 'scrum_master_id'];
            const missingFields = [];
            
            requiredFields.forEach(field => {
                const value = formData.get(field);
                if (!value || value.trim() === '') {
                    missingFields.push(field);
                }
            });
            
            if (missingFields.length > 0) {
                console.error('Campos requeridos faltantes:', missingFields);
                e.preventDefault();
                alert('Error: Los siguientes campos son requeridos:\n' + missingFields.join(', '));
                return;
            }
            
            console.log('Todos los campos requeridos están presentes, enviando formulario...');
            
            // Mostrar loading en el botón
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando...';
            submitBtn.disabled = true;
            
            // Restaurar botón después de 10 segundos (por si hay error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 10000);
        });
    } else {
        console.error('ERROR: Formulario modalCrearProyecto no encontrado');
    }
});
</script>
@endpush
