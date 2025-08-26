@extends('fabricasoft::layouts.app')
@section('title', 'Equipo Scrum - ' . $project->project_name)

@php
    use \Modules\FABRICASOFT\Entities\PhaseInfoHistory;
@endphp

@section('content')

<div class="container-fluid">
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
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Header del Proyecto -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram me-2"></i>{{ $project->project_name }}
        </h1>
        <div>
            <a href="{{ route('fabricasoft.desarrollador.projects') }}" class="btn btn-sena me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver a Proyectos
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
                            <p class="text-muted">{{ $project->description }}</p>
                            
                            @if($project->project_goals)
                                <p><strong>Metas del Proyecto:</strong></p>
                                <p class="text-muted">{{ $project->project_goals }}</p>
                            @endif
                            
                            @if($project->success_criteria)
                                <p><strong>Criterios de Éxito:</strong></p>
                                <p class="text-muted">{{ $project->success_criteria }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Estado:</strong> 
                                @if($project->status == 'planning')
                                    <span class="badge bg-warning">{{ $project->status_text }}</span>
                                @elseif($project->status == 'active')
                                    <span class="badge bg-info">{{ $project->status_text }}</span>
                                @elseif($project->status == 'completed')
                                    <span class="badge bg-success">{{ $project->status_text }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $project->status_text }}</span>
                                @endif
                            </p>
                            
                            <p><strong>Scrum Master:</strong> {{ $project->scrumMaster->nickname ?? $project->scrumMaster->name }}</p>
                            <p><strong>Cliente:</strong> 
                                @if($project->preregistration)
                                    {{ $project->preregistration->full_name }}
                                    @if($project->preregistration->organization)
                                        ({{ $project->preregistration->organization }})
                                    @else
                                        (Organización no disponible)
                                    @endif
                                @else
                                    Cliente no disponible
                                @endif
                            </p>
                            
                            @if($project->start_date)
                                <p><strong>Fecha de Inicio:</strong> {{ $project->start_date->format('d/m/Y') }}</p>
                            @endif
                            
                            @if($project->estimated_end_date)
                                <p><strong>Fecha de Finalización Estimada:</strong> {{ $project->estimated_end_date->format('d/m/Y') }}</p>
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
                        <span class="badge bg-primary fs-6">{{ $project->teamMembers->where('status', 'active')->count() + 1 }} miembros activos</span>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Scrum Master: 1</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Desarrolladores: {{ $project->developers->count() }}</small>
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
                                        <h6 class="mb-1">{{ $project->scrumMaster->nickname ?? 'Sin nombre' }}</h6>
                                        <p class="mb-1 text-muted">{{ $project->scrumMaster->email }}</p>
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
                @if($project->teamMembers->where('status', 'active')->count() > 0)
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
                                @foreach($project->teamMembers->where('status', 'active') as $member)
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
                $allPhases = $project->phases->sortBy('order');
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
                                                            <p class="text-muted mb-0">{{ $project->preregistration->full_name ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Email:</strong>
                                                            <p class="text-muted mb-0">{{ $project->preregistration->email ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Teléfono:</strong>
                                                            <p class="text-muted mb-0">{{ $project->preregistration->phone ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Empresa/Organización:</strong>
                                                            <p class="text-muted mb-0">{{ $project->preregistration->organization ?? 'No especificado' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <strong>Tipo de Software:</strong>
                                                            <p class="text-muted mb-0">{{ $project->preregistration->software_type ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Descripción del Proyecto:</strong>
                                                            <p class="text-muted mb-0">{{ $project->preregistration->project_description ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Requisitos Adicionales:</strong>
                                                            <p class="text-muted mb-0">{{ $project->preregistration->additional_requirements ?? 'No especificado' }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Tipo de Cliente:</strong>
                                                            <p class="text-muted mb-0">{{ ucfirst(str_replace('_', ' ', $project->preregistration->client_type ?? 'No especificado')) }}</p>
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
                                                            <span class="ms-2">{{ $project->preregistration->created_at ? $project->preregistration->created_at->format('d/m/Y') : 'No especificada' }}</span>
                                                        </div>
                                                        @if($project->preregistration->reviewed_at)
                                                        <div class="mb-2">
                                                            <small class="text-muted">Fecha de Aprobación:</small>
                                                            <span class="ms-2">{{ $project->preregistration->reviewed_at->format('d/m/Y') }}</span>
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
                                                                                                 <div class="d-flex justify-content-between align-items-center mb-3">
                                                     <h6 class="text-success mb-0">
                                                         <i class="fas fa-check-circle me-2"></i>Información de la Fase
                                                     </h6>
                                                                                                         <button type="button" class="btn btn-success btn-sm" onclick="agregarInformacionFase({{ $completedPhase->id }})">
                                                        <i class="fas fa-plus me-2"></i>Agregar Información
                                                    </button>
                                                 </div>
                                                
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
                                                                    <th><i class="fas fa-cogs me-2"></i>Acciones</th>
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
                                                                            
                                                                            // Buscar la rama en el campo notes (puede estar en diferentes posiciones)
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
                                                                                // Siempre intentar obtener el nombre del usuario desde la base de datos
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
                                                                            <td>
                                                                                <div class="btn-group btn-group-sm" role="group">
                                                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="editarInformacionFase({{ $historyItem->id }})" title="Editar">
                                                                                        <i class="fas fa-edit"></i>
                                                                                    </button>
                                                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarInformacionFase({{ $historyItem->id }})" title="Eliminar">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="7" class="text-center text-muted py-4">
                                                                            <i class="fas fa-code fa-2x mb-3 d-block"></i>
                                                                            <strong>No hay información de desarrollo registrada</strong><br>
                                                                            <small>Agrega información sobre módulos, commits y repositorios</small>
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
                                                                    <th><i class="fas fa-cogs me-2"></i>Acciones</th>
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
                                                                            <td>
                                                                                <div class="btn-group btn-group-sm" role="group">
                                                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="editarInformacionFase({{ $historyItem->id }})" title="Editar">
                                                                                        <i class="fas fa-edit"></i>
                                                                                    </button>
                                                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarInformacionFase({{ $historyItem->id }})" title="Eliminar">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="6" class="text-center text-muted py-4">
                                                                            <i class="fas fa-server fa-2x mb-3 d-block"></i>
                                                                            <strong>No hay información de implementación registrada</strong><br>
                                                                            <small>Agrega información sobre usuario, contraseña, dominio y URL completo del sistema</small>
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
                                                                    <th>Acciones</th>
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
                                                                        <tr>
                                                                            <td>{{ $historyItem->description ?: 'No especificada' }}</td>
                                                                            <td>{{ $historyItem->notes ?: 'No especificadas' }}</td>
                                                                            <td>
                                                                                @if($historyItem->external_link)
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
                                                                                <div class="btn-group btn-group-sm" role="group">
                                                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="editarInformacionFase({{ $historyItem->id }})">
                                                                                        <i class="fas fa-edit"></i>
                                                                                    </button>
                                                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarInformacionFase({{ $historyItem->id }})">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </div>
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

<!-- Modal para Agregar Información a Fase -->
<div class="modal fade" id="modalAgregarInformacion" tabindex="-1" aria-labelledby="modalAgregarInformacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalAgregarInformacionLabel">
                    <i class="fas fa-plus me-2"></i>Agregar Información a la Fase
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAgregarInformacion" action="{{ route('fabricasoft.desarrollador.projects.phase.info.add', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="info_phase_id" name="phase_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Agrega nueva información a esta fase.</strong> Cada vez que agregues información se creará una nueva fila en la tabla de la fase.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="info_descripcion" class="form-label">Descripción *</label>
                                <textarea class="form-control" id="info_descripcion" name="nueva_descripcion" 
                                          rows="4" placeholder="Describe la nueva información o comentario..." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="info_notas" class="form-label">Notas</label>
                                <textarea class="form-control" id="info_notas" name="nuevas_notas" 
                                          rows="3" placeholder="Notas adicionales, observaciones..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="info_comentario" class="form-label">Comentario Adicional</label>
                                <textarea class="form-control" id="info_comentario" name="comentario_adicional" 
                                          rows="2" placeholder="Comentario o contexto adicional..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="info_enlace" class="form-label">Enlace</label>
                                <input type="url" class="form-control" id="info_enlace" name="nuevo_enlace" 
                                       placeholder="https://example.com/documento">
                                <div class="form-text">Enlace a documentación externa o recursos relacionados.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="info_documento" class="form-label">Documento</label>
                                <input type="file" class="form-control" id="info_documento" name="nuevo_documento" 
                                       accept=".pdf,.doc,.docx,.txt">
                                <div class="form-text">Sube un documento relacionado (PDF, DOC, DOCX, TXT).</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="info_fecha_creacion" class="form-label">Fecha de Creación</label>
                                <input type="date" class="form-control" id="info_fecha_creacion" name="fecha_creacion" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Agregar Información
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Información de Fase -->
<div class="modal fade" id="modalEditarInformacion" tabindex="-1" aria-labelledby="modalEditarInformacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalEditarInformacionLabel">
                    <i class="fas fa-edit me-2"></i>Editar Información de la Fase
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarInformacion" action="{{ route('fabricasoft.desarrollador.projects.phase.info.update.new', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_history_id" name="history_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Edita la información de esta fase. Los cambios se aplicarán inmediatamente.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_descripcion" class="form-label">Descripción *</label>
                                <textarea class="form-control" id="edit_descripcion" name="descripcion" 
                                          rows="4" placeholder="Describe la información de la fase..." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_notas" class="form-label">Notas</label>
                                <textarea class="form-control" id="edit_notas" name="notas" 
                                          rows="3" placeholder="Notas adicionales, observaciones..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_comentario_adicional" class="form-label">Comentario Adicional</label>
                                <textarea class="form-control" id="edit_comentario_adicional" name="comentario_adicional" 
                                          rows="2" placeholder="Comentario o contexto adicional..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_enlace" class="form-label">Enlace</label>
                                <input type="url" class="form-control" id="edit_enlace" name="enlace" 
                                       placeholder="https://example.com/documento">
                                <div class="form-text">Enlace a documentación externa o recursos relacionados.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_fecha_creacion" class="form-label">Fecha de Creación</label>
                                <input type="date" class="form-control" id="edit_fecha_creacion" name="fecha_creacion">
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_documento" class="form-label">Nuevo Documento (opcional)</label>
                                <input type="file" class="form-control" id="edit_documento" name="documento" 
                                       accept=".pdf,.doc,.docx,.txt">
                                <div class="form-text">Sube un nuevo documento para reemplazar el existente.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales individuales para cada fase -->
@foreach($project->phases->where('status', 'completed') as $completedPhase)
    <div class="modal fade" id="modalAgregarInformacion{{ $completedPhase->id }}" tabindex="-1" aria-labelledby="modalAgregarInformacionLabel{{ $completedPhase->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAgregarInformacionLabel{{ $completedPhase->id }}">
                        <i class="fas fa-plus me-2"></i>Agregar Información a {{ $completedPhase->phase_name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregarInformacion{{ $completedPhase->id }}" action="{{ route('fabricasoft.desarrollador.projects.phase.info.add.specific', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="phase_id" value="{{ $completedPhase->id }}">
                    <input type="hidden" name="phase_order" value="{{ $completedPhase->order }}">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Agrega nueva información a la fase <strong>{{ $completedPhase->phase_name }}</strong>. Cada vez que agregues información se creará una nueva fila en la tabla de la fase.
                        </div>
                        
                        @if($completedPhase->order == 4)
                            <!-- Campos específicos para Fase 4 (Codificación) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="info_modulo{{ $completedPhase->id }}" class="form-label">Módulo *</label>
                                        <input type="text" class="form-control" id="info_modulo{{ $completedPhase->id }}" name="modulo" 
                                               placeholder="Ej: Autenticación, Dashboard, API, etc." required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_desarrollador{{ $completedPhase->id }}" class="form-label">Desarrollador *</label>
                                        <select class="form-select" id="info_desarrollador{{ $completedPhase->id }}" name="desarrollador" required>
                                            <option value="">Selecciona un desarrollador</option>
                                            @foreach($project->teamMembers->where('status', 'active') as $member)
                                                @if($member->role === 'developer')
                                                    <option value="{{ $member->user->id }}">
                                                        {{ $member->user->nickname ?? $member->user->name ?? 'Sin nombre' }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_fecha_desarrollo{{ $completedPhase->id }}" class="form-label">Fecha de Desarrollo *</label>
                                        <input type="date" class="form-control" id="info_fecha_desarrollo{{ $completedPhase->id }}" name="fecha_desarrollo" 
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="info_nombre_commit{{ $completedPhase->id }}" class="form-label">Commit *</label>
                                        <input type="text" class="form-control" id="info_nombre_commit{{ $completedPhase->id }}" name="nombre_commit" 
                                               placeholder="Ej: feat: implementa autenticación de usuarios" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_url_repositorio{{ $completedPhase->id }}" class="form-label">Repositorio *</label>
                                        <input type="url" class="form-control" id="info_url_repositorio{{ $completedPhase->id }}" name="url_repositorio" 
                                               placeholder="https://github.com/usuario/repositorio" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_rama{{ $completedPhase->id }}" class="form-label">Rama (opcional)</label>
                                        <input type="text" class="form-control" id="info_rama{{ $completedPhase->id }}" name="rama" 
                                               placeholder="Ej: develop, feature/auth, main">
                                    </div>
                                </div>
                            </div>
                        @elseif($completedPhase->order == 6)
                            <!-- Campos específicos para Fase 6 (Implementación) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="info_descripcion{{ $completedPhase->id }}" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="info_descripcion{{ $completedPhase->id }}" name="descripcion" 
                                                  rows="4" placeholder="Describe la implementación realizada..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_usuario{{ $completedPhase->id }}" class="form-label">Usuario *</label>
                                        <input type="text" class="form-control" id="info_usuario{{ $completedPhase->id }}" name="usuario" 
                                               placeholder="Usuario de acceso al sistema" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_contrasena{{ $completedPhase->id }}" class="form-label">Contraseña *</label>
                                        <input type="password" class="form-control" id="info_contrasena{{ $completedPhase->id }}" name="contrasena" 
                                               placeholder="Contraseña de acceso al sistema" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="info_dominio{{ $completedPhase->id }}" class="form-label">Dominio *</label>
                                        <input type="text" class="form-control" id="info_dominio{{ $completedPhase->id }}" name="dominio" 
                                               placeholder="sistema.ejemplo.com" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_url_completo{{ $completedPhase->id }}" class="form-label">URL Completo *</label>
                                        <input type="url" class="form-control" id="info_url_completo{{ $completedPhase->id }}" name="url_completo" 
                                               placeholder="https://sistema.ejemplo.com/login" required>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Campos generales para otras fases -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="info_descripcion{{ $completedPhase->id }}" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="info_descripcion{{ $completedPhase->id }}" name="descripcion" 
                                                  rows="4" placeholder="Describe la información que quieres agregar a esta fase..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_notas{{ $completedPhase->id }}" class="form-label">Notas</label>
                                        <textarea class="form-control" id="info_notas{{ $completedPhase->id }}" name="notas" 
                                                  rows="3" placeholder="Notas adicionales, observaciones..."></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_fecha_creacion{{ $completedPhase->id }}" class="form-label">Fecha de Creación</label>
                                        <input type="date" class="form-control" id="info_fecha_creacion{{ $completedPhase->id }}" name="fecha_creacion" 
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="info_enlace{{ $completedPhase->id }}" class="form-label">Enlace</label>
                                        <input type="url" class="form-control" id="info_enlace{{ $completedPhase->id }}" name="enlace" 
                                               placeholder="https://example.com/documento">
                                        <div class="form-text">Enlace a documentación externa o recursos relacionados.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_documento{{ $completedPhase->id }}" class="form-label">Documento (opcional)</label>
                                        <input type="file" class="form-control" id="info_documento{{ $completedPhase->id }}" name="documento" 
                                               accept=".pdf,.doc,.docx,.txt">
                                        <div class="form-text">Sube un documento relacionado con esta fase.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="info_comentario_adicional{{ $completedPhase->id }}" class="form-label">Comentario Adicional</label>
                                        <textarea class="form-control" id="info_comentario_adicional{{ $completedPhase->id }}" name="comentario_adicional" 
                                                  rows="2" placeholder="Comentario o contexto adicional..."></textarea>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Agregar Información
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal para Completar Fase con Información -->
<!-- Modales separados para completar fase según el tipo -->
@foreach($project->phases->where('status', '!=', 'completed') as $phase)
    <div class="modal fade" id="modalCompletarFase{{ $phase->id }}" tabindex="-1" aria-labelledby="modalCompletarFase{{ $phase->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalCompletarFase{{ $phase->id }}Label">
                        <i class="fas fa-check-circle me-2"></i>Completar {{ $phase->phase_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formCompletarFase{{ $phase->id }}" action="{{ route('fabricasoft.desarrollador.projects.phase.complete.with.info.simple', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="phase_id" value="{{ $phase->id }}">
                    <input type="hidden" name="phase_order" value="{{ $phase->order }}">
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Estás por completar {{ $phase->phase_name }}.</strong> Agrega la información final de la fase antes de marcarla como completada.
                        </div>
                        
                        @if($phase->order == 4)
                            <!-- Campos específicos para Fase 4 (Codificación) -->
                            <div class="alert alert-warning">
                                <i class="fas fa-code me-2"></i>
                                <strong>Fase 4: Codificación</strong> - Campos específicos para desarrollo de software
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="complete_modulo{{ $phase->id }}" class="form-label">Módulo *</label>
                                        <input type="text" class="form-control" id="complete_modulo{{ $phase->id }}" name="modulo" 
                                               placeholder="Ej: Autenticación, Dashboard, API, etc." required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_desarrollador{{ $phase->id }}" class="form-label">Desarrollador *</label>
                                        <select class="form-select" id="complete_desarrollador{{ $phase->id }}" name="desarrollador" required>
                                            <option value="">Selecciona un desarrollador</option>
                                            @foreach($project->teamMembers->where('status', 'active') as $member)
                                                @if($member->role === 'developer')
                                                    <option value="{{ $member->user->id }}">
                                                        {{ $member->user->nickname ?? $member->user->name ?? 'Sin nombre' }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_fecha_desarrollo{{ $phase->id }}" class="form-label">Fecha de Desarrollo *</label>
                                        <input type="date" class="form-control" id="complete_fecha_desarrollo{{ $phase->id }}" name="fecha_desarrollo" 
                                               value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="complete_nombre_commit{{ $phase->id }}" class="form-label">Commit *</label>
                                        <input type="text" class="form-control" id="complete_nombre_commit{{ $phase->id }}" name="nombre_commit" 
                                               placeholder="Ej: feat: implementa autenticación de usuarios" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_url_repositorio{{ $phase->id }}" class="form-label">Repositorio *</label>
                                        <input type="url" class="form-control" id="complete_url_repositorio{{ $phase->id }}" name="url_repositorio" 
                                               placeholder="https://github.com/usuario/repositorio" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_rama{{ $phase->id }}" class="form-label">Rama (opcional)</label>
                                        <input type="text" class="form-control" id="complete_rama{{ $phase->id }}" name="rama" 
                                               placeholder="Ej: develop, feature/auth, main">
                                    </div>
                                </div>
                            </div>
                        @elseif($phase->order == 6)
                            <!-- Campos específicos para Fase 6 (Implementación) -->
                            <div class="alert alert-warning">
                                <i class="fas fa-server me-2"></i>
                                <strong>Fase 6: Implementación</strong> - Campos específicos para implementación del sistema
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="complete_descripcion{{ $phase->id }}" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="complete_descripcion{{ $phase->id }}" name="descripcion" 
                                                  rows="4" placeholder="Describe la implementación realizada..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_usuario{{ $phase->id }}" class="form-label">Usuario *</label>
                                        <input type="text" class="form-control" id="complete_usuario{{ $phase->id }}" name="usuario" 
                                               placeholder="Usuario de acceso al sistema" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_contrasena{{ $phase->id }}" class="form-label">Contraseña *</label>
                                        <input type="password" class="form-control" id="complete_contrasena{{ $phase->id }}" name="contrasena" 
                                               placeholder="Contraseña de acceso al sistema" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="complete_dominio{{ $phase->id }}" class="form-label">Dominio *</label>
                                        <input type="text" class="form-control" id="complete_dominio{{ $phase->id }}" name="dominio" 
                                               placeholder="sistema.ejemplo.com" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_url_completo{{ $phase->id }}" class="form-label">URL Completo *</label>
                                        <input type="url" class="form-control" id="complete_url_completo{{ $phase->id }}" name="url_completo" 
                                               placeholder="https://sistema.ejemplo.com/login" required>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Campos generales para otras fases -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="complete_descripcion{{ $phase->id }}" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="complete_descripcion{{ $phase->id }}" name="descripcion" 
                                                  rows="4" placeholder="Describe la información que quieres agregar a esta fase..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_notas{{ $phase->id }}" class="form-label">Notas</label>
                                        <textarea class="form-control" id="complete_notas{{ $phase->id }}" name="notas" 
                                                  rows="3" placeholder="Notas adicionales, observaciones..."></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_fecha_creacion{{ $phase->id }}" class="form-label">Fecha de Creación</label>
                                        <input type="date" class="form-control" id="complete_fecha_creacion{{ $phase->id }}" name="fecha_creacion" 
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="complete_enlace{{ $phase->id }}" class="form-label">Enlace</label>
                                        <input type="url" class="form-control" id="complete_enlace{{ $phase->id }}" name="enlace" 
                                               placeholder="https://example.com/documento">
                                        <div class="form-text">Enlace a documentación externa o recursos relacionados.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_documento{{ $phase->id }}" class="form-label">Documento (opcional)</label>
                                        <input type="file" class="form-control" id="complete_documento{{ $phase->id }}" name="documento" 
                                               accept=".pdf,.doc,.docx,.txt">
                                        <div class="form-text">Sube un documento relacionado con esta fase.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_comentario_adicional{{ $phase->id }}" class="form-label">Comentario Adicional</label>
                                        <textarea class="form-control" id="complete_comentario_adicional{{ $phase->id }}" name="comentario_adicional" 
                                                  rows="2" placeholder="Comentario o contexto adicional..."></textarea>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-check me-2"></i>Completar Fase
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

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

function agregarInformacionFase(phaseId) {
    console.log('agregarInformacionFase llamada con phaseId:', phaseId);
    
    try {
        // Abrir el modal específico de la fase
        const modalElement = document.getElementById(`modalAgregarInformacion${phaseId}`);
        
        if (!modalElement) {
            console.error(`Modal modalAgregarInformacion${phaseId} no encontrado`);
            alert('Error: Modal no encontrado');
            return;
        }
        
        // Verificar si Bootstrap está disponible
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap no está disponible, usando método alternativo');
        }
        
        // Mostrar el modal
        console.log('Intentando mostrar modal específico...');
        
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal específico mostrado con Bootstrap');
        } else {
            // Fallback: mostrar modal manualmente
            modalElement.classList.add('show');
            modalElement.style.display = 'block';
            modalElement.setAttribute('aria-hidden', 'false');
            
            // Agregar backdrop
            const backdrop = document.createElement('div');
            backdrop.classList.add('modal-backdrop', 'fade', 'show');
            backdrop.id = 'modalBackdrop';
            document.body.appendChild(backdrop);
            
            console.log('Modal específico mostrado manualmente (fallback)');
        }
        
    } catch (error) {
        console.error('Error en agregarInformacionFase:', error);
        alert('Error al abrir el modal: ' + error.message);
    }
}

function editarInformacionFase(historyId) {
    console.log('editarInformacionFase llamada con historyId:', historyId);
    
    try {
        // Obtener la información de la fase para editar
        fetch(`/fabricasoft/desarrollador/proyecto/{{ $project->id }}/fase/informacion/${historyId}/info`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Llenar el modal de edición con la información existente
                    document.getElementById('edit_history_id').value = historyId;
                    document.getElementById('edit_descripcion').value = data.data.content || '';
                    document.getElementById('edit_notas').value = data.data.notes || '';
                    document.getElementById('edit_enlace').value = data.data.external_link || '';
                    document.getElementById('edit_comentario_adicional').value = data.data.additional_comment || '';
                    document.getElementById('edit_fecha_creacion').value = data.data.start_date || '';
                    
                    // Mostrar el modal de edición
                    const modal = new bootstrap.Modal(document.getElementById('modalEditarInformacion'));
                    modal.show();
                } else {
                    alert('Error al obtener información de la fase: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al obtener información de la fase para editar.');
            });
    } catch (error) {
        console.error('Error en editarInformacionFase:', error);
        alert('Error al abrir el modal de edición: ' + error.message);
    }
}

function eliminarInformacionFase(historyId, phaseId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta información? Esta acción no se puede deshacer.')) {
        console.log('eliminarInformacionFase llamada con historyId:', historyId, 'phaseId:', phaseId);
        
        try {
            // Enviar solicitud para eliminar el elemento del historial
            fetch(`/fabricasoft/desarrollador/proyecto/{{ $project->id }}/fase/informacion/${historyId}/eliminar`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Información eliminada exitosamente.');
                    location.reload();
                } else {
                    alert('Error al eliminar la información: ' + data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la información.');
            });
        } catch (error) {
            console.error('Error en eliminarInformacionFase:', error);
            alert('Error al eliminar la información: ' + error.message);
        }
    }
}

// Event listener para el formulario de agregar información
document.addEventListener('DOMContentLoaded', function() {
    const formAgregarInformacion = document.getElementById('formAgregarInformacion');
    if (formAgregarInformacion) {
        console.log('Formulario de agregar información encontrado');
        
        formAgregarInformacion.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Formulario enviado');
            
            const formData = new FormData(this);
            
            // Log de los datos del formulario
            console.log('Datos del formulario:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Verificar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token no encontrado');
                alert('Error: Token CSRF no encontrado');
                return;
            }
            
            console.log('Enviando formulario a:', this.action);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                },
            }).then(response => {
                console.log('Respuesta recibida:', response.status, response.statusText);
                return response.json();
            }).then(data => {
                console.log('Datos de respuesta:', data);
                
                if (data.success) {
                    alert('Información agregada exitosamente a la fase.');
                    
                    // Cerrar modal
                    const modal = document.getElementById('modalAgregarInformacion');
                    if (modal) {
                        const bootstrapModal = bootstrap.Modal.getInstance(modal);
                        if (bootstrapModal) {
                            bootstrapModal.hide();
                        }
                    }
                    
                    // Recargar página
                    location.reload();
                } else {
                    alert('Error al agregar información: ' + (data.message || 'Error desconocido'));
                }
            }).catch(error => {
                console.error('Error en fetch:', error);
                alert('Error al agregar información a la fase: ' + error.message);
            });
        });
    } else {
        console.error('Formulario de agregar información NO encontrado');
    }
    
    // Event listener para el formulario de editar información
    const formEditarInformacion = document.getElementById('formEditarInformacion');
    if (formEditarInformacion) {
        console.log('Formulario de editar información encontrado');
        
        formEditarInformacion.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Formulario de edición enviado');
            
            const formData = new FormData(this);
            
            // Log de los datos del formulario
            console.log('Datos del formulario de edición:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Verificar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token no encontrado');
                alert('Error: Token CSRF no encontrado');
                return;
            }
            
            console.log('Enviando formulario de edición a:', this.action);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                },
            }).then(response => {
                console.log('Respuesta recibida:', response.status, response.statusText);
                return response.json();
            }).then(data => {
                console.log('Datos de respuesta:', data);
                
                if (data.success) {
                    alert('Información actualizada exitosamente.');
                    
                    // Cerrar modal
                    const modal = document.getElementById('modalEditarInformacion');
                    if (modal) {
                        const bootstrapModal = bootstrap.Modal.getInstance(modal);
                        if (bootstrapModal) {
                            bootstrapModal.hide();
                        }
                    }
                    
                    // Recargar página
                    location.reload();
                } else {
                    alert('Error al actualizar la información: ' + (data.message || 'Error desconocido'));
                }
            }).catch(error => {
                console.error('Error en fetch:', error);
                alert('Error al actualizar la información: ' + error.message);
            });
        });
    } else {
        console.error('Formulario de editar información NO encontrado');
    }
});
</script>
@endpush
