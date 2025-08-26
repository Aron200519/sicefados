@extends('fabricasoft::layouts.app')
@section('title', 'Equipo Scrum - ' . $proyecto->project_name)

@php
    use \Modules\FABRICASOFT\Entities\PhaseInfoHistory;
@endphp

@section('content')

<div class="container-fluid">
    <!-- Header del Proyecto -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram me-2"></i>{{ $proyecto->project_name }}
        </h1>
        <div>
            <a href="{{ route('fabricasoft.cliente_interno.projects') }}" class="btn btn-sena me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Mis Proyectos
            </a>
        </div>
    </div>

    <!-- Información del Proyecto -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información del Proyecto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Descripción:</strong></p>
                            <p class="text-muted">{{ $proyecto->description }}</p>
                            
                            @if($proyecto->project_goals)
                                <p><strong>Metas del Proyecto:</strong></p>
                                <p class="text-muted">{{ $proyecto->project_goals }}</p>
                            @endif
                            
                            @if($proyecto->success_criteria)
                                <p><strong>Criterios de Éxito:</strong></p>
                                <p class="text-muted">{{ $proyecto->success_criteria }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Estado:</strong> 
                                @if($proyecto->status == 'planning')
                                    <span class="badge bg-warning">{{ $proyecto->status_text }}</span>
                                @elseif($proyecto->status == 'active')
                                    <span class="badge bg-info">{{ $proyecto->status_text }}</span>
                                @elseif($proyecto->status == 'completed')
                                    <span class="badge bg-success">{{ $proyecto->status_text }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $proyecto->status_text }}</span>
                                @endif
                            </p>
                            
                            <p><strong>Scrum Master:</strong> {{ $proyecto->scrumMaster->nickname ?? $proyecto->scrumMaster->name }}</p>
                            <p><strong>Cliente:</strong> 
                                @if($proyecto->preregistration)
                                    {{ $proyecto->preregistration->full_name }}
                                    @if($proyecto->preregistration->organization)
                                        ({{ $proyecto->preregistration->organization }})
                                    @else
                                        (Organización no disponible)
                                    @endif
                                @else
                                    Cliente no disponible
                                @endif
                            </p>
                            
                            @if($proyecto->start_date)
                                <p><strong>Fecha de Inicio:</strong> {{ $proyecto->start_date->format('d/m/Y') }}</p>
                            @endif
                            
                            @if($proyecto->estimated_end_date)
                                <p><strong>Fecha de Finalización Estimada:</strong> {{ $proyecto->estimated_end_date->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>Equipo del Proyecto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-primary fs-6">{{ $proyecto->teamMembers->where('status', 'active')->count() + 1 }} miembros activos</span>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Scrum Master: 1</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Desarrolladores: {{ $proyecto->developers->count() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Miembros del Equipo -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>Miembros del Equipo
            </h6>
        </div>
        <div class="card-body">
            <!-- Scrum Master (Siempre visible) -->
            <div class="mb-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-crown me-2"></i>Scrum Master
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user-tie fa-2x text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $proyecto->scrumMaster->nickname ?? 'Sin nombre' }}</h6>
                                        <p class="mb-1 text-muted">{{ $proyecto->scrumMaster->email }}</p>
                                        <span class="badge bg-primary">Scrum Master</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Otros Miembros del Equipo -->
            <div class="mb-4">
                <h6 class="text-info mb-3">
                    <i class="fas fa-users me-2"></i>Equipo de Desarrollo
                </h6>
                @if($proyecto->teamMembers->where('status', 'active')->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Celular</th>
                                    <th>Rol</th>
                                    <th>Responsabilidades</th>
                                    <th>Fecha de Ingreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proyecto->teamMembers->where('status', 'active') as $member)
                                <tr>
                                    <td>{{ $member->user->nickname ?? 'Sin nombre' }}</td>
                                    <td>{{ $member->user->email }}</td>
                                    <td>{{ $member->user->phone ?? 'No especificado' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $member->role_text }}</span>
                                    </td>
                                    <td>{{ $member->responsibilities ?: 'No especificadas' }}</td>
                                    <td>{{ $member->joined_date->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No hay desarrolladores en el equipo</h5>
                        <p class="text-gray-400">El Scrum Master agregará desarrolladores al proyecto.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- NUEVAS FASES DEL PROYECTO -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-tasks me-2"></i>Fases del Proyecto
            </h6>
        </div>
        <div class="card-body">
            @php
                $allPhases = $proyecto->phases->sortBy('order');
                $completedPhases = $allPhases->where('status', 'completed');
                $currentPhase = $allPhases->where('status', 'in_progress')->first();
                
                // Si no hay fase en progreso, buscar la siguiente pendiente
                if (!$currentPhase) {
                    $currentPhase = $allPhases->where('status', 'planned')->first();
                }
                
                // Buscar la siguiente fase disponible
                $nextPhase = null;
                if ($currentPhase && $currentPhase->status === 'completed') {
                    $nextPhase = $allPhases->where('order', '>', $currentPhase->order)
                                          ->where('status', 'planned')
                                          ->first();
                }
            @endphp
            
            <!-- FASES ANTERIORES COMPLETADAS (ACORDEÓN) -->
            @if($completedPhases->count() > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-check-circle me-2"></i>Fases Completadas ({{ $completedPhases->count() }})
                    </h6>

                    <div class="accordion" id="accordionCompletedPhases">
                        @foreach($completedPhases as $index => $completedPhase)
                        <div class="accordion-item border-left-success">
                            <h2 class="accordion-header" id="headingCompleted{{ $completedPhase->id }}">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapseCompleted{{ $completedPhase->id }}" 
                                        aria-expanded="false" aria-controls="collapseCompleted{{ $completedPhase->id }}">
                                    <div class="d-flex align-items-center justify-content-between w-100">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success me-3">✅ Completada</span>
                                            <span class="badge bg-secondary me-3">Fase {{ $completedPhase->order }}</span>
                                            <h6 class="mb-0">{{ $completedPhase->phase_name }}</h6>
                                        </div>
                                        <div class="d-flex align-items-center me-3">
                                            @if($completedPhase->end_date)
                                                <small class="text-muted">{{ $completedPhase->end_date->format('d/m/Y') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseCompleted{{ $completedPhase->id }}" class="accordion-collapse collapse" 
                                 aria-labelledby="headingCompleted{{ $completedPhase->id }}" data-bs-parent="#accordionCompletedPhases">
                                <div class="accordion-body">
                                    @if($completedPhase->order == 1)
                                        <!-- FASE 1: DEFINICIÓN DE NECESIDADES - Información del Pre-registro -->
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6 class="text-success mb-3">
                                                    <i class="fas fa-user-check me-2"></i>Información del Pre-registro
                                                </h6>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <strong>Solicitante:</strong>
                                                            <p class="text-muted mb-0">{{ $proyecto->preregistration->full_name ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Email:</strong>
                                                            <p class="text-muted mb-0">{{ $proyecto->preregistration->email ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Teléfono:</strong>
                                                            <p class="text-muted mb-0">{{ $proyecto->preregistration->phone ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Empresa/Organización:</strong>
                                                            <p class="text-muted mb-0">{{ $proyecto->preregistration->organization ?? 'No especificado' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <strong>Tipo de Software:</strong>
                                                            <p class="text-muted mb-0">{{ $proyecto->preregistration->software_type ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Descripción del Proyecto:</strong>
                                                            <p class="text-muted mb-0">{{ $proyecto->preregistration->project_description ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Requisitos Adicionales:</strong>
                                                            <p class="text-muted mb-0">{{ $proyecto->preregistration->additional_requirements ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Tipo de Cliente:</strong>
                                                            <p class="text-muted mb-0">{{ ucfirst(str_replace('_', ' ', $proyecto->preregistration->client_type ?? 'No especificado')) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-title text-muted">Estado de la Solicitud</h6>
                                                        <div class="mb-2">
                                                            <small class="text-muted">Estado:</small>
                                                            <span class="badge bg-success ms-2">Aprobado</span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <small class="text-muted">Fecha de Solicitud:</small>
                                                            <span class="ms-2">{{ $proyecto->preregistration->created_at ? $proyecto->preregistration->created_at->format('d/m/Y') : 'No especificada' }}</span>
                                                        </div>
                                                        @if($proyecto->preregistration->reviewed_at)
                                                        <div class="mb-2">
                                                            <small class="text-muted">Fecha de Aprobación:</small>
                                                            <span class="ms-2">{{ $proyecto->preregistration->reviewed_at->format('d/m/Y') }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- FASES 2+: Mostrar información de la fase completada -->
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="text-success mb-3">
                                                    <i class="fas fa-check-circle me-2"></i>Información de la Fase
                                                </h6>
                                                
                                                <div class="table-responsive">
                                                    @if($completedPhase->order == 4)
                                                        <!-- Tabla especial para Fase 4 (Codificación) -->
                                                        <table class="table table-sm table-bordered table-hover">
                                                            <thead class="table-primary">
                                                                <tr>
                                                                    <th><i class="fas fa-code me-2"></i>Módulo</th>
                                                                    <th><i class="fas fa-user me-2"></i>Desarrollador</th>
                                                                    <th><i class="fas fa-calendar me-2"></i>Fecha Desarrollo</th>
                                                                    <th><i class="fas fa-git-alt me-2"></i>Commit</th>
                                                                    <th><i class="fas fa-link me-2"></i>Repositorio</th>
                                                                    <th><i class="fas fa-code-branch me-2"></i>Rama</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $phaseHistory = \Modules\FABRICASOFT\Entities\PhaseInfoHistory::where('phase_id', $completedPhase->id)
                                                                        ->where('info_type', 'complete_info')
                                                                        ->orderBy('created_at', 'desc')
                                                                        ->get();
                                                                @endphp
                                                                
                                                                @if($phaseHistory->count() > 0)
                                                                    @foreach($phaseHistory as $historyItem)
                                                                        @php
                                                                            // Extraer información de los campos específicos de Fase 4
                                                                            $descParts = explode(' | ', $historyItem->description);
                                                                            $modulo = str_replace('Módulo: ', '', $descParts[0] ?? '');
                                                                            $commit = str_replace('Commit: ', '', $descParts[1] ?? '');
                                                                            
                                                                            $notesParts = explode(' | ', $historyItem->notes);
                                                                            $desarrolladorId = str_replace('Desarrollador: ', '', $notesParts[0] ?? '');
                                                                            
                                                                            // Buscar la rama en el campo notes
                                                                            $rama = '';
                                                                            foreach ($notesParts as $part) {
                                                                                if (strpos($part, 'Rama: ') === 0) {
                                                                                    $rama = str_replace('Rama: ', '', $part);
                                                                                    break;
                                                                                }
                                                                            }
                                                                            
                                                                            // Obtener el nombre del desarrollador
                                                                            $desarrollador = 'No especificado';
                                                                            if ($desarrolladorId && is_numeric($desarrolladorId)) {
                                                                                try {
                                                                                    $user = \App\Models\User::find($desarrolladorId);
                                                                                    if ($user) {
                                                                                        $desarrollador = $user->nickname ?? $user->name ?? 'Sin nombre';
                                                                                    } else {
                                                                                        $desarrollador = 'Usuario no encontrado';
                                                                                    }
                                                                                } catch (\Exception $e) {
                                                                                    $desarrollador = 'Usuario no disponible';
                                                                                }
                                                                            }
                                                                            
                                                                            $fechaDesarrollo = str_replace('Fecha de desarrollo: ', '', $historyItem->additional_comment ?? '');
                                                                        @endphp
                                                                        <tr>
                                                                            <td>
                                                                                <span class="badge bg-info">{{ $modulo ?: 'No especificado' }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge bg-secondary">{{ $desarrollador ?: 'No especificado' }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <small class="text-muted">{{ $fechaDesarrollo ? \Carbon\Carbon::parse($fechaDesarrollo)->format('d/m/Y') : 'No especificada' }}</small>
                                                                            </td>
                                                                            <td>
                                                                                <code class="text-primary">{{ $commit ?: 'No especificado' }}</code>
                                                                            </td>
                                                                            <td>
                                                                                @if($historyItem->external_link)
                                                                                    <a href="{{ $historyItem->external_link }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                                                        <i class="fab fa-github me-1"></i>Ver Repo
                                                                                    </a>
                                                                                @else
                                                                                    <span class="text-muted">No especificado</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($rama && $rama !== 'No especificada')
                                                                                    <span class="badge bg-warning">{{ $rama }}</span>
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="6" class="text-center text-muted py-4">
                                                                            <i class="fas fa-code fa-2x mb-3 d-block"></i>
                                                                            <strong>No hay información de desarrollo registrada</strong><br>
                                                                            <small>El equipo de desarrollo agregará información sobre módulos, commits y repositorios</small>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    @elseif($completedPhase->order == 6)
                                                        <!-- Tabla específica para Fase 6 (Implementación) -->
                                                        <table class="table table-sm table-bordered table-hover">
                                                            <thead class="table-info">
                                                                <tr>
                                                                    <th><i class="fas fa-server me-2"></i>Descripción</th>
                                                                    <th><i class="fas fa-user me-2"></i>Usuario</th>
                                                                    <th><i class="fas fa-lock me-2"></i>Contraseña</th>
                                                                    <th><i class="fas fa-globe me-2"></i>Dominio</th>
                                                                    <th><i class="fas fa-external-link-alt me-2"></i>URL Completo</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $phaseHistory = \Modules\FABRICASOFT\Entities\PhaseInfoHistory::where('phase_id', $completedPhase->id)
                                                                        ->where('info_type', 'complete_info')
                                                                        ->orderBy('created_at', 'desc')
                                                                        ->get();
                                                                @endphp
                                                                
                                                                @if($phaseHistory->count() > 0)
                                                                    @foreach($phaseHistory as $historyItem)
                                                                        @php
                                                                            // Extraer información de los campos específicos de Fase 6
                                                                            $descripcion = $historyItem->description ?? '';
                                                                            
                                                                            $notesParts = explode(' | ', $historyItem->notes ?? '');
                                                                            $usuario = '';
                                                                            $contrasena = '';
                                                                            
                                                                            foreach ($notesParts as $part) {
                                                                                if (strpos($part, 'Usuario: ') === 0) {
                                                                                    $usuario = str_replace('Usuario: ', '', $part);
                                                                                }
                                                                                if (strpos($part, 'Contraseña: ') === 0) {
                                                                                    $contrasena = str_replace('Contraseña: ', '', $part);
                                                                                }
                                                                            }
                                                                            
                                                                            $url = $historyItem->external_link ?? '';
                                                                            
                                                                            $commentParts = explode(' | ', $historyItem->additional_comment ?? '');
                                                                            $dominio = '';
                                                                            $urlCompleto = '';
                                                                            
                                                                            foreach ($commentParts as $part) {
                                                                                if (strpos($part, 'Dominio: ') === 0) {
                                                                                    $dominio = str_replace('Dominio: ', '', $part);
                                                                                }
                                                                                if (strpos($part, 'URL Completo: ') === 0) {
                                                                                    $urlCompleto = str_replace('URL Completo: ', '', $part);
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        <tr>
                                                                            <td>
                                                                                <span class="text-primary">{{ $descripcion ?: 'No especificada' }}</span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge bg-secondary">{{ $usuario ?: 'No especificado' }}</span>
                                                                            </td>
                                                                            <td>
                                                                                @if($contrasena)
                                                                                    <div class="input-group input-group-sm">
                                                                                        <input type="password" class="form-control form-control-sm" value="{{ $contrasena }}" readonly style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">
                                                                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePasswordVisibility(this)">
                                                                                            <i class="fas fa-eye"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                @else
                                                                                    <span class="text-muted">No especificada</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge bg-info">{{ $dominio ?: 'No especificado' }}</span>
                                                                            </td>
                                                                            <td>
                                                                                @if($urlCompleto)
                                                                                    <a href="{{ $urlCompleto }}" target="_blank" class="btn btn-outline-success btn-sm">
                                                                                        <i class="fas fa-external-link-alt me-1"></i>Ver URL
                                                                                    </a>
                                                                                @else
                                                                                    <span class="text-muted">No especificada</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="5" class="text-center text-muted py-4">
                                                                            <i class="fas fa-server fa-2x mb-3 d-block"></i>
                                                                            <strong>No hay información de implementación registrada</strong><br>
                                                                            <small>El equipo de desarrollo agregará información sobre usuario, contraseña, dominio y URL completo del sistema</small>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <!-- Tabla general para otras fases -->
                                                        <table class="table table-sm table-bordered table-hover">
                                                            <thead class="table-success">
                                                                <tr>
                                                                    <th>Descripción</th>
                                                                    <th>Notas</th>
                                                                    <th>Enlace</th>
                                                                    <th>Documento</th>
                                                                    <th>Fecha de Creación</th>
                                                                    <th>Comentario Adicional</th>
                                                                    <th>Creado por</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    // Usar la información cargada desde el controlador
                                                                    $phaseHistory = $completedPhase->phaseInfoHistory ?? collect();
                                                                @endphp
                                                                
                                                                @if($phaseHistory->count() > 0)
                                                                    @foreach($phaseHistory as $historyItem)
                                                                        <tr>
                                                                            <td>{{ $historyItem->content ?: 'No especificada' }}</td>
                                                                            <td>{{ $historyItem->notes ?: 'No especificadas' }}</td>
                                                                            <td>
                                                                                <if($historyItem->external_link)
                                                                                    <a href="{{ $historyItem->external_link }}" target="_blank" class="text-primary">
                                                                                        <i class="fas fa-external-link-alt me-2"></i>Ver Enlace
                                                                                    </a>
                                                                                @else
                                                                                    <span class="text-muted">No especificado</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($historyItem->document_path)
                                                                                    <a href="{{ Storage::url($historyItem->document_path) }}" target="_blank" class="text-primary">
                                                                                        <i class="fas fa-file-download me-2"></i>Descargar
                                                                                    </a>
                                                                                @else
                                                                                    <span class="text-muted">No subido</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ $historyItem->start_date ? \Carbon\Carbon::parse($historyItem->start_date)->format('d/m/Y') : 'No especificada' }}</td>
                                                                            <td>{{ $historyItem->additional_comment ?: 'No especificado' }}</td>
                                                                            <td>
                                                                                @if($historyItem->addedByUser)
                                                                                    <span class="badge bg-info">
                                                                                        <i class="fas fa-user me-1"></i>
                                                                                        {{ $historyItem->addedByUser->name ?? $historyItem->addedByUser->email ?? 'Usuario' }}
                                                                                    </span>
                                                                                @else
                                                                                    <span class="text-muted">No disponible</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="7" class="text-center text-muted">
                                                                            <i class="fas fa-info-circle me-2"></i>No hay información adicional para esta fase
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- FASE ACTUAL EN PROGRESO -->
            @if($currentPhase && $currentPhase->status === 'in_progress')
                <div class="mb-4">
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-play-circle me-2"></i>Fase Actual en Progreso
                    </h6>
                    
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <span class="badge bg-warning me-3">🔄 En Progreso</span>
                                    <span class="badge bg-secondary me-3">Fase {{ $currentPhase->order }}</span>
                                    <h6 class="mb-0">{{ $currentPhase->phase_name }}</h6>
                                </div>
                                <div>
                                    @if($currentPhase->start_date)
                                        <small class="text-muted">Iniciada: {{ $currentPhase->start_date->format('d/m/Y') }}</small>
                                    @endif
                                </div>
                            </div>
                            
                            @if($currentPhase->description)
                                <p class="text-muted mb-3">{{ $currentPhase->description }}</p>
                            @endif
                            
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 75%;" 
                                     aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                                    75% Completado
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        @if($currentPhase->start_date)
                                            Inicio: {{ $currentPhase->start_date->format('d/m/Y') }}
                                        @else
                                            Fecha de inicio no especificada
                                        @endif
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        @if($currentPhase->end_date)
                                            Estimado: {{ $currentPhase->end_date->format('d/m/Y') }}
                                        @else
                                            Fecha de finalización no especificada
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- PRÓXIMAS FASES PLANIFICADAS -->
            @php
                $plannedPhases = $allPhases->where('status', 'planned');
            @endphp
            
            @if($plannedPhases->count() > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-clock me-2"></i>Próximas Fases Planificadas ({{ $plannedPhases->count() }})
                    </h6>
                    
                    <div class="row">
                        @foreach($plannedPhases as $plannedPhase)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-left-secondary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-secondary me-2">⏳ Planificada</span>
                                        <span class="badge bg-light text-dark">Fase {{ $plannedPhase->order }}</span>
                                    </div>
                                    
                                    <h6 class="mb-2">{{ $plannedPhase->phase_name }}</h6>
                                    
                                    @if($plannedPhase->description)
                                        <p class="text-muted small mb-2">{{ $plannedPhase->description }}</p>
                                    @endif
                                    
                                    <div class="text-muted small">
                                        @if($plannedPhase->start_date)
                                            <div><i class="fas fa-calendar me-1"></i>Inicio: {{ $plannedPhase->start_date->format('d/m/Y') }}</div>
                                        @endif
                                        @if($plannedPhase->end_date)
                                            <div><i class="fas fa-calendar-check me-1"></i>Fin: {{ $plannedPhase->end_date->format('d/m/Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePasswordVisibility(button) {
    // Buscar el input dentro del mismo input-group
    const inputGroup = button.closest('.input-group');
    const input = inputGroup.querySelector('input');
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        button.title = 'Ocultar contraseña';
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        button.title = 'Mostrar contraseña';
    }
}
</script>
@endpush
