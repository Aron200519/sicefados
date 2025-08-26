@extends('fabricasoft::layouts.app')
@section('title', 'Dashboard Cliente Interno - FABRICASOFT')
@section('content')

<!-- Header del Dashboard -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-handshake me-2" style="color: var(--sena-green);"></i>
            Dashboard Cliente Interno
        </h2>
        <p class="text-muted mb-0">Panel de seguimiento de proyectos contratados internamente</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('fabricasoft.cliente_interno.projects') }}" class="btn btn-sena-outline">
            <i class="fas fa-list me-2"></i>Ver Todos los Proyectos
        </a>
        @php
            $proyectoActivo = $proyectos->whereIn('status', ['planning', 'active'])->first();
        @endphp
        @if(!$proyectoActivo)
            <a href="{{ route('fabricasoft.cliente_interno.nueva-solicitud') }}" class="btn btn-sena">
                <i class="fas fa-plus me-2"></i>Nueva Solicitud
            </a>
        @else
            <button class="btn btn-secondary" disabled title="No puedes crear una nueva solicitud mientras tengas un proyecto activo">
                <i class="fas fa-plus me-2"></i>Nueva Solicitud
            </button>
        @endif
    </div>
</div>

<!-- Mensajes de Sesión -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Contenido Principal -->
<div class="row">
    <div class="col-md-12 mb-4">
        <!-- Mis Solicitudes -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Mis Solicitudes Enviadas
                </h5>
            </div>
            <div class="card-body">
                @if($todasLasSolicitudes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Descripción del Proyecto</th>
                                    <th>Tipo de Software</th>
                                    <th>Estado</th>
                                    <th>Fecha de Envío</th>
                                    <th>Notas del Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todasLasSolicitudes as $solicitud)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ Str::limit($solicitud->project_description ?: 'Solicitud #' . $solicitud->id, 40) }}</strong>
                                                @if($solicitud->additional_requirements)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ Str::limit($solicitud->additional_requirements, 50) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $solicitud->software_type ?? 'No especificado' }}</span>
                                        </td>
                                        <td>
                                            @if($solicitud->status == 'pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Pendiente
                                                </span>
                                            @elseif($solicitud->status == 'approved')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Aprobada
                                                </span>
                                            @elseif($solicitud->status == 'rejected')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Rechazada
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($solicitud->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $solicitud->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($solicitud->admin_notes)
                                                <span class="text-muted" title="{{ $solicitud->admin_notes }}">
                                                    {{ Str::limit($solicitud->admin_notes, 30) }}
                                                </span>
                                            @else
                                                <span class="text-muted">Sin notas</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No has enviado solicitudes</h5>
                        <p class="text-muted">Envía tu primera solicitud para comenzar a trabajar con nosotros.</p>
                        <a href="{{ route('fabricasoft.cliente_interno.nueva-solicitud') }}" class="btn btn-sena">
                            <i class="fas fa-plus me-2"></i>Crear Primera Solicitud
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.stats-card {
    background: linear-gradient(135deg, var(--sena-green), #2E7D32);
    color: white;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.stats-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 10px 10px 0 0 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.progress {
    border-radius: 10px;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}
</style>
@endpush
