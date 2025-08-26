@extends('fabricasoft::layouts.app')
@section('title', 'Dashboard Cliente Externo - FABRICASOFT')
@section('content')

<!-- Header del Dashboard -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-handshake me-2" style="color: var(--sena-green);"></i>
            Dashboard Cliente Externo
        </h2>
        <p class="text-muted mb-0">Panel de seguimiento de proyectos contratados externamente</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('cliente_externo.projects') }}" class="btn btn-sena-outline">
            <i class="fas fa-list me-2"></i>Ver Todos los Proyectos
        </a>
        @php
            $proyectoActivo = $proyectos->whereIn('status', ['planning', 'active'])->first();
        @endphp
        @if(!$proyectoActivo)
            <a href="{{ route('cliente_externo.nueva-solicitud') }}" class="btn btn-sena">
                <i class="fas fa-plus me-2"></i>Nueva Solicitud
            </a>
        @else
            <button class="btn btn-secondary" disabled title="No puedes crear una nueva solicitud mientras tengas un proyecto activo">
                <i class="fas fa-plus me-2"></i>Nueva Solicitud
            </button>
        @endif
    </div>
</div>





<!-- Contenido Principal -->
<div class="row">
    <!-- Mis Solicitudes -->
    <div class="col-md-12 mb-4">
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
                                @foreach($todasLasSolicitudes->take(5) as $solicitud)
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
                    
                    @if($todasLasSolicitudes->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('cliente_externo.nueva-solicitud') }}" class="btn btn-sena-outline">
                                <i class="fas fa-list me-2"></i>Ver Todas las Solicitudes ({{ $todasLasSolicitudes->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No has enviado solicitudes</h5>
                        <p class="text-muted">Envía tu primera solicitud para comenzar a trabajar con nosotros.</p>
                        <a href="{{ route('cliente_externo.nueva-solicitud') }}" class="btn btn-sena">
                            <i class="fas fa-plus me-2"></i>Crear Primera Solicitud
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>








@endsection
