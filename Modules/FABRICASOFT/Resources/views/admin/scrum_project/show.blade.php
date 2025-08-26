@extends('fabricasoft::layouts.app')
@section('title', 'Equipo Scrum - ' . $project->project_name)

@php
    use \Modules\FABRICASOFT\Entities\PhaseInfoHistory;
@endphp

@section('content')

<div class="container-fluid">
    <!-- Header del Proyecto -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram me-2"></i>{{ $project->project_name }}
        </h1>
        <div>
            <a href="{{ route('fabricasoft.admin.projects.list') }}" class="btn btn-sena me-2">
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

    <!-- EQUIPO FINAL DEL PROYECTO -->
    @if($project->status === 'completed' && $project->delivered_at)
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-users-cog me-2"></i>Equipo Final del Proyecto
                </h6>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success me-2">
                        <i class="fas fa-check me-1"></i>Proyecto Entregado
                    </span>
                    <span class="badge bg-info">
                        <i class="fas fa-users me-1"></i>Equipo Final
                    </span>
                </div>
            </div>
            <div class="card-body">
                @php
                    // Obtener información del equipo final desde las tablas existentes
                    $scrumMaster = $project->scrumMaster;
                    $teamMembers = $project->teamMembers()->with('user')->get();
                    $hasFinalTeam = $scrumMaster || $teamMembers->count() > 0;
                @endphp
                
                @if($hasFinalTeam)
                    <!-- Resumen del Equipo Final -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded bg-light">
                                <i class="fas fa-crown fa-2x text-primary mb-2"></i>
                                <h6 class="mb-1">Scrum Master</h6>
                                <span class="badge bg-primary">{{ $scrumMaster ? '1' : '0' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded bg-light">
                                <i class="fas fa-code fa-2x text-success mb-2"></i>
                                <h6 class="mb-1">Desarrolladores</h6>
                                <span class="badge bg-success">{{ $teamMembers->count() }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 border rounded bg-light">
                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                <h6 class="mb-1">Total Equipo</h6>
                                <span class="badge bg-info">{{ ($scrumMaster ? 1 : 0) + $teamMembers->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle del Equipo Final -->
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-list me-2"></i>Detalle de Participantes
                    </h6>
                    
                    <div class="row">
                        <!-- Scrum Master -->
                        @if($scrumMaster)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-header bg-primary text-white text-center">
                                        <i class="fas fa-crown me-2"></i>Scrum Master
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-user-tie text-primary fa-3x"></i>
                                        </div>
                                        <h6 class="card-title mb-2">
                                            {{ $scrumMaster->nickname ?? $scrumMaster->name ?? 'Usuario' }}
                                        </h6>
                                        <p class="card-text small text-muted mb-3">
                                            {{ $scrumMaster->email }}
                                        </p>
                                        <div class="mb-3">
                                            <strong class="text-muted">Responsabilidades:</strong>
                                            <p class="card-text small mt-1">
                                                Liderazgo del equipo y gestión del proyecto
                                            </p>
                                        </div>
                                        <div class="row text-muted small">
                                            <div class="col-6">
                                                <i class="fas fa-play me-1"></i>
                                                <strong>Inicio:</strong><br>
                                                {{ \Carbon\Carbon::parse($project->created_at)->format('d/m/Y') }}
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-flag-checkered me-1"></i>
                                                <strong>Finalizó:</strong><br>
                                                {{ \Carbon\Carbon::parse($project->delivered_at)->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Miembros del Equipo -->
                        @foreach($teamMembers as $member)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-success h-100">
                                    <div class="card-header bg-success text-white text-center">
                                        <i class="fas fa-code me-2"></i>{{ ucfirst($member->role) }}
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-user-gear text-success fa-3x"></i>
                                        </div>
                                        <h6 class="card-title mb-2">
                                            {{ $member->user->nickname ?? $member->user->name ?? 'Usuario' }}
                                        </h6>
                                        <p class="card-text small text-muted mb-3">
                                            {{ $member->user->email }}
                                        </p>
                                        @if($member->responsibilities)
                                            <div class="mb-3">
                                                <strong class="text-muted">Responsabilidades:</strong>
                                                <p class="card-text small mt-1">
                                                    {{ $member->responsibilities }}
                                                </p>
                                            </div>
                                        @endif
                                        <div class="row text-muted small">
                                            <div class="col-6">
                                                <i class="fas fa-play me-1"></i>
                                                <strong>Inicio:</strong><br>
                                                {{ \Carbon\Carbon::parse($member->created_at)->format('d/m/Y') }}
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-flag-checkered me-1"></i>
                                                <strong>Finalizó:</strong><br>
                                                {{ \Carbon\Carbon::parse($project->delivered_at)->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Información de Entrega -->
                    <div class="mt-4">
                        <div class="alert alert-success">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-2">
                                        <i class="fas fa-calendar-check me-2"></i>
                                        Información de Entrega del Proyecto
                                    </h6>
                                    <div class="row text-muted small">
                                        <div class="col-md-6">
                                            <i class="fas fa-calendar me-1"></i>
                                            <strong>Fecha de Entrega:</strong> 
                                            {{ \Carbon\Carbon::parse($project->delivered_at)->format('d/m/Y \a \l\a\s H:i') }}
                                        </div>
                                        @if($project->delivered_by)
                                            <div class="col-md-6">
                                                <i class="fas fa-user me-1"></i>
                                                <strong>Entregado por:</strong> 
                                                {{ \App\Models\User::find($project->delivered_by)->name ?? 'Usuario' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fas fa-trophy fa-3x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No se encontró información del equipo final</h5>
                        <p class="text-gray-400">La información del equipo se mostrará cuando se entregue el proyecto.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Miembros del Equipo - Solo visible para proyectos activos -->
    @if($project->status !== 'completed' || !$project->delivered_at)
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users me-2"></i>Miembros del Equipo
                </h6>
                @if(Auth::id() == $project->scrum_master_id || Auth::user()->hasCustomRole('fabricasoft.admin'))
                    <button type="button" class="btn btn-sena btn-sm" onclick="mostrarModalAgregarDesarrollador()">
                        <i class="fas fa-code me-2"></i>Agregar Desarrollador
                    </button>
                @endif
            </div>
            <div class="card-body">
                <!-- Scrum Master (Solo para proyectos activos) -->
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
                                        <th>Acciones</th>
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
                                        <td>
                                            @if(Auth::id() == $project->scrum_master_id || Auth::user()->hasCustomRole('fabricasoft.admin'))
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="removerMiembro({{ $member->id }})">
                                                    <i class="fas fa-user-minus"></i> Remover
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No hay desarrolladores en el equipo</h5>
                            <p class="text-gray-400">Agrega desarrolladores al proyecto para comenzar el desarrollo.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

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
                                                            <p class="text-muted mb-0">{{ $project->request->full_name }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Email:</strong>
                                                            <p class="text-muted mb-0">{{ $project->request->email }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Teléfono:</strong>
                                                            <p class="text-muted mb-0">{{ $project->request->phone }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Empresa/Organización:</strong>
                                                            <p class="text-muted mb-0">{{ $project->request->organization }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <strong>Tipo de Software:</strong>
                                                            <p class="text-muted mb-0">{{ $project->request->software_type }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Descripción del Proyecto:</strong>
                                                            <p class="text-muted mb-0">{{ $project->request->project_description }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Requisitos Adicionales:</strong>
                                                            <p class="text-muted mb-0">{{ $project->request->additional_requirements }}</p>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <strong>Tipo de Cliente:</strong>
                                                            <p class="text-muted mb-0">{{ ucfirst(str_replace('_', ' ', $project->request->client_type)) }}</p>
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
                                                            <span class="ms-2">{{ $project->request->created_at->format('d/m/Y') }}</span>
                                                        </div>
                                                        @if($project->request->reviewed_at)
                                                        <div class="mb-2">
                                                            <small class="text-muted">Fecha de Aprobación:</small>
                                                            <span class="ms-2">{{ $project->request->reviewed_at->format('d/m/Y') }}</span>
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
            
            @if($currentPhase)
                                 <!-- FASE ACTUAL -->
                <div class="card mb-4 border-left-{{ $currentPhase->status === 'completed' ? 'success' : ($currentPhase->status === 'in_progress' ? 'warning' : 'primary') }}">
                    <div class="card-header bg-{{ $currentPhase->status === 'completed' ? 'success' : ($currentPhase->status === 'in_progress' ? 'warning' : 'primary') }} text-white">
                        <h6 class="mb-0">
                            @if($currentPhase->order == 1)
                                <i class="fas fa-clipboard-list me-2"></i>Fase {{ $currentPhase->order }}: {{ $currentPhase->phase_name }}
                            @else
                                <i class="fas fa-tasks me-2"></i>Fase {{ $currentPhase->order }}: {{ $currentPhase->phase_name }}
                            @endif
                            
                            <!-- Estado de la fase actual -->
                            <span class="badge bg-light text-dark ms-3">
                                @if($currentPhase->status === 'completed')
                                    ✅ Completada
                                @elseif($currentPhase->status === 'in_progress')
                                    🔄 En Progreso
                                @else
                                    ⏳ Pendiente
                                @endif
                            </span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- INFORMACIÓN ACTUAL DE LA FASE -->
                        <div class="text-center py-4">
                            <i class="fas fa-{{ $currentPhase->order == 1 ? 'clipboard-list' : 'tasks' }} fa-3x text-primary mb-3"></i>
                            <h4 class="text-primary">{{ $currentPhase->phase_name }}</h4>
                            <p class="text-muted mb-4">
                                @if($currentPhase->order == 1)
                                    Esta fase incluye toda la información del pre-registro del cliente y define las necesidades del proyecto.
                                @else
                                    {{ $currentPhase->description ?: 'Fase de desarrollo del proyecto de software.' }}
                                @endif
                            </p>
                            
                            <!-- ESTADO ACTUAL -->
                            <div class="mb-4">
                                @if($currentPhase->status === 'completed')
                                    <span class="badge bg-success fs-6 px-3 py-2">✅ Fase Completada</span>
                                @elseif($currentPhase->status === 'in_progress')
                                    <span class="badge bg-warning fs-6 px-3 py-2">🔄 En Progreso</span>
                                @else
                                    <span class="badge bg-primary fs-6 px-3 py-2">⏳ Lista para Iniciar</span>
                                @endif
                            </div>
                            
                            <!-- BOTONES DE ACCIÓN -->
                            <div class="d-flex justify-content-center gap-3">
                                @if($currentPhase->status === 'planned' || $currentPhase->status === 'in_progress')
                                    <button type="button" class="btn btn-primary btn-lg" onclick="completarFase({{ $currentPhase->id }})">
                                        <i class="fas fa-check me-2"></i>Completar Fase {{ $currentPhase->order }}
                                    </button>
                                @endif
                                
                                @if($currentPhase->status === 'completed')
                                    <button type="button" class="btn btn-outline-primary" onclick="mostrarModalFase({{ $currentPhase->id }})">
                                        <i class="fas fa-eye me-2"></i>Ver Detalles
                                    </button>
                                @endif
                                
                                @if($currentPhase->status === 'completed' && $nextPhase)
                                    <button type="button" class="btn btn-success btn-lg" onclick="avanzarSiguienteFase({{ $nextPhase->id }})">
                                        <i class="fas fa-arrow-right me-2"></i>Siguiente Fase
                                    </button>
                                @endif
                                

                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- PROGRESO DEL PROYECTO -->
            <div class="mt-4">
                <h6 class="text-muted mb-3">Progreso del Proyecto</h6>
                @php
                    $completedPhases = $project->phases()->where('status', 'completed')->count();
                    $totalPhases = $project->phases->count();
                    $progressPercentage = $totalPhases > 0 ? ($completedPhases / $totalPhases) * 100 : 0;
                @endphp
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%" 
                         aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        {{ round($progressPercentage) }}%
                    </div>
                </div>
                <small class="text-muted">{{ $completedPhases }} de {{ $totalPhases }} fases completadas</small>
                
                @if($progressPercentage == 100)
                    @if($project->status !== 'completed' || !$project->delivered_at)
                        <!-- BOTÓN DE ENTREGAR PROYECTO -->
                        <div class="mt-3 text-center">
                            <div class="alert alert-success">
                                <i class="fas fa-trophy me-2"></i>
                                <strong>¡Proyecto Completado!</strong> Todas las fases han sido finalizadas exitosamente.
                            </div>
                            <button type="button" class="btn btn-success btn-lg" id="btnEntregarProyecto" data-project-id="{{ $project->id }}" data-clicked="false" onclick="entregarProyecto(this, {{ $project->id }})">
                                <i class="fas fa-rocket me-2"></i>Entregar Proyecto
                            </button>
                            
                            <script>
                                // Verificar estado del botón al cargar la página
                                document.addEventListener('DOMContentLoaded', function() {
                                    const btnEntregar = document.getElementById('btnEntregarProyecto');
                                    if (btnEntregar) {
                                        const projectId = btnEntregar.getAttribute('data-project-id');
                                        const buttonState = localStorage.getItem('proyecto_entregado_' + projectId);
                                        
                                        if (buttonState === 'true') {
                                            // El botón ya fue presionado, deshabilitarlo
                                            btnEntregar.disabled = true;
                                            btnEntregar.innerHTML = '<i class="fas fa-check-circle me-2"></i>¡PROYECTO ENTREGADO!';
                                            btnEntregar.className = 'btn btn-success btn-lg';
                                            btnEntregar.title = 'Proyecto ya entregado - NO se puede volver a presionar';
                                            btnEntregar.setAttribute('data-clicked', 'true');
                                        }
                                    }
                                });
                            </script>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Al entregar el proyecto, se marcará como finalizado, se liberará al equipo y se notificará a todos los stakeholders.
                                </small>
                            </div>
                        </div>
                    @else
                        <!-- PROYECTO YA ENTREGADO -->
                        <div class="mt-3 text-center">
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Proyecto Entregado!</strong> Este proyecto ha sido finalizado y entregado exitosamente.
                            </div>
                            
                            <!-- INFORMACIÓN DE PARTICIPANTES -->
                            <div class="mt-4">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-users me-2"></i>Equipo que Participó en el Proyecto
                                </h6>
                                
                                @php
                                    // Obtener participantes del proyecto desde la tabla de participantes
                                    $participants = \DB::table('fabricasoft_project_participants')
                                        ->where('project_id', $project->id)
                                        ->where('end_reason', 'project_delivered')
                                        ->get();
                                @endphp
                                
                                @if($participants->count() > 0)
                                    <div class="row">
                                        @foreach($participants as $participant)
                                            @php
                                                $user = \App\Models\User::find($participant->user_id);
                                                $roleColor = $participant->role === 'scrum_master' ? 'primary' : 'success';
                                                $roleIcon = $participant->role === 'scrum_master' ? 'fa-crown' : 'fa-code';
                                            @endphp
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border-{{ $roleColor }} h-100">
                                                    <div class="card-body text-center">
                                                        <div class="mb-2">
                                                            <i class="fas {{ $roleIcon }} text-{{ $roleColor }} fa-2x"></i>
                                                        </div>
                                                        <h6 class="card-title mb-1">
                                                            {{ $user ? ($user->nickname ?? $user->name ?? 'Usuario') : 'Usuario Desconocido' }}
                                                        </h6>
                                                        <span class="badge bg-{{ $roleColor }} mb-2">
                                                            @if($participant->role === 'scrum_master')
                                                                Scrum Master
                                                            @else
                                                                {{ ucfirst($participant->role) }}
                                                            @endif
                                                        </span>
                                                        @if($participant->responsibilities)
                                                            <p class="card-text small text-muted">
                                                                <strong>Responsabilidades:</strong><br>
                                                                {{ $participant->responsibilities }}
                                                            </p>
                                                        @endif
                                                        <div class="small text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            Participó desde: {{ \Carbon\Carbon::parse($participant->started_at)->format('d/m/Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No se encontraron registros de participantes del proyecto.
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    Entregado el: {{ $project->delivered_at ? \Carbon\Carbon::parse($project->delivered_at)->format('d/m/Y H:i') : 'N/A' }}
                                    @if($project->delivered_by)
                                        <br><i class="fas fa-user me-1"></i>
                                        Por: {{ \App\Models\User::find($project->delivered_by)->name ?? 'Usuario' }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endif
                @endif
                
                <!-- LISTA DE TODAS LAS FASES (COLLAPSIBLE) -->
                <div class="mt-4">
                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAllPhases" aria-expanded="false">
                        <i class="fas fa-list me-2"></i>Ver Todas las Fases
                    </button>
                    
                    <div class="collapse mt-3" id="collapseAllPhases">
                        <div class="card card-body">
                            <h6 class="text-muted mb-3">Resumen de Todas las Fases</h6>
                            <div class="row">
                                @foreach($allPhases as $phase)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ $phase->status === 'completed' ? 'success' : ($phase->status === 'in_progress' ? 'warning' : 'secondary') }} me-2">
                                            @if($phase->status === 'completed')
                                                ✅
                                            @elseif($phase->status === 'in_progress')
                                                🔄
                                            @else
                                                ⏳
                                            @endif
                                        </span>
                                        <span class="me-2">Fase {{ $phase->order }}:</span>
                                        <strong>{{ $phase->phase_name }}</strong>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipo Final del Proyecto -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trophy me-2"></i>Equipo Final del Proyecto
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Proyecto Completado:</strong> Este es el equipo que terminó exitosamente el proyecto.
                        </div>
                        
                        <div class="row">
                            <!-- Scrum Master -->
                            <div class="col-md-6 mb-3">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-crown me-2"></i>Scrum Master
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($project->scrumMaster)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $project->scrumMaster->nickname ?? $project->scrumMaster->name ?? 'Sin nombre' }}</h6>
                                                    <p class="text-muted mb-0">{{ $project->scrumMaster->email }}</p>
                                                                            @if($project->status === 'completed' && $project->delivered_at)
                            <div class="alert alert-success small mt-2 mb-0">
                                <i class="fas fa-trophy me-1"></i>
                                <strong>Estado:</strong> 🎉 LIBRE para nuevos proyectos (Proyecto ENTREGADO)
                            </div>
                        @elseif($project->status === 'completed')
                            <div class="alert alert-info small mt-2 mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Estado:</strong> Libre para el próximo proyecto
                            </div>
                        @else
                            <small class="text-success">
                                <i class="fas fa-check me-1"></i>Liderando el proyecto actualmente
                            </small>
                        @endif
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No hay Scrum Master asignado</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Desarrolladores -->
                            <div class="col-md-6 mb-3">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-code me-2"></i>Desarrolladores
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $completedDevelopers = $project->teamMembers->where('role', 'developer')->where('status', '!=', 'removed');
                                        @endphp
                                        
                                        @if($completedDevelopers->count() > 0)
                                            @foreach($completedDevelopers as $developer)
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="fas fa-code"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $developer->user->nickname ?? $developer->user->name ?? 'Sin nombre' }}</h6>
                                                        <p class="text-muted mb-0">{{ $developer->user->email }}</p>
                                                        @if($developer->responsibilities)
                                                            <small class="text-primary">
                                                                <i class="fas fa-tasks me-1"></i>{{ $developer->responsibilities }}
                                                            </small>
                                                        @endif
                                                        @if($project->status === 'completed' && $project->delivered_at)
                                                            <div class="alert alert-success small mt-2 mb-0">
                                                                <i class="fas fa-trophy me-1"></i>
                                                                <strong>Estado:</strong> 🎉 LIBRE para nuevos proyectos (Proyecto ENTREGADO)
                                                            </div>
                                                        @elseif($project->status === 'completed')
                                                            <div class="alert alert-info small mt-2 mb-0">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                <strong>Estado:</strong> Libre para el próximo proyecto
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(!$loop->last)
                                                    <hr class="my-2">
                                                @endif
                                            @endforeach
                                        @else
                                            <p class="text-muted mb-0">No hay desarrolladores asignados</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen del Equipo -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-chart-pie me-2"></i>Resumen del Equipo
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <div class="border-end">
                                                    <h4 class="text-success mb-1">1</h4>
                                                    <p class="text-muted mb-0">Scrum Master</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="border-end">
                                                    <h4 class="text-primary mb-1">{{ $project->teamMembers->where('role', 'developer')->where('status', '!=', 'removed')->count() }}</h4>
                                                    <p class="text-muted mb-0">Desarrolladores</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h4 class="text-info mb-1">{{ 1 + $project->teamMembers->where('role', 'developer')->where('status', '!=', 'removed')->count() }}</h4>
                                                <p class="text-muted mb-0">Total del Equipo</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Desarrollador -->
<div class="modal fade" id="modalAgregarDesarrollador" tabindex="-1" aria-labelledby="modalAgregarDesarrolladorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarDesarrolladorLabel">
                    <i class="fas fa-code me-2"></i>Agregar Desarrollador al Equipo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('fabricasoft.admin.projects.add.member', $project->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                                    <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Desarrolladores Disponibles:</strong> Se muestran SOLO usuarios con rol de desarrollador que no estén asignados a este proyecto específico. Los desarrolladores pueden trabajar en múltiples proyectos simultáneamente.
                </div>
                    
                    <div class="mb-3">
                        @php
                            $developerCount = $availableUsers->count();
                        @endphp
                        

                        
                        <label for="dev_user_id" class="form-label">
                            Desarrollador * 
                            <span class="badge bg-info ms-2">
                                {{ $developerCount }} desarrollador{{ $developerCount != 1 ? 'es' : '' }}
                            </span>
                        </label>
                        <select class="form-select" id="dev_user_id" name="user_id" required>
                            <option value="">Selecciona un desarrollador</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->nickname ?? 'Sin nombre' }} ({{ $user->id }})
                                </option>
                            @endforeach
                        </select>
                        @if($developerCount == 0)
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                No hay desarrolladores disponibles en este momento. Todos están asignados a otros proyectos activos o no hay desarrolladores con rol asignado en el sistema.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Rol fijo como desarrollador -->
                    <input type="hidden" name="role" value="developer">
                    
                    <div class="mb-3">
                        <label for="dev_responsibilities" class="form-label">Responsabilidades Específicas</label>
                        <textarea class="form-control" id="dev_responsibilities" name="responsibilities" 
                                  rows="3" placeholder="Ej: Frontend, Backend, Base de datos, APIs, etc."></textarea>
                        <div class="form-text">Describe las tecnologías o áreas específicas en las que trabajará este desarrollador.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-code me-2"></i>Agregar Desarrollador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Fase 2: Análisis y SRS -->
<div class="modal fade" id="modalFase2" tabindex="-1" aria-labelledby="modalFase2Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalFase2Label">
                    <i class="fas fa-search me-2"></i>Fase 2: Análisis y SRS
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formFase2" action="{{ route('fabricasoft.admin.projects.phase2.save', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Completa la información de la fase de Análisis y SRS. Esta información será fundamental para el desarrollo del proyecto.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fase" class="form-label">Fase *</label>
                                <input type="text" class="form-control" id="fase" name="fase" value="Análisis y SRS" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción *</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" 
                                          rows="4" placeholder="Describe detalladamente el análisis realizado y los requisitos del sistema..." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea class="form-control" id="notas" name="notas" 
                                          rows="3" placeholder="Notas adicionales, observaciones, consideraciones especiales..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="enlace" class="form-label">Enlace</label>
                                <input type="url" class="form-control" id="enlace" name="enlace" 
                                       placeholder="https://example.com/documento-srs">
                                <div class="form-text">Enlace a documentación externa, repositorio, o recursos relacionados.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="file" class="form-control" id="documento" name="documento" 
                                       accept=".pdf,.doc,.docx,.txt">
                                <div class="form-text">Sube el documento SRS o análisis técnico (PDF, DOC, DOCX, TXT).</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="inicio" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="inicio" name="inicio" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="fin" class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control" id="fin" name="fin" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="seguirSiguienteFase()">
                        <i class="fas fa-arrow-right me-2"></i>Seguir a la Siguiente Fase
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
                        <i class="fas fa-plus me-2"></i>Agregar Información a {{ $completedPhase->title }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregarInformacion{{ $completedPhase->id }}" action="{{ route('fabricasoft.admin.projects.phase.info.add', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="phase_id" value="{{ $completedPhase->id }}">
                    <input type="hidden" name="phase_order" value="{{ $completedPhase->order }}">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Agrega nueva información a la fase <strong>{{ $completedPhase->title }}</strong>. Cada vez que agregues información se creará una nueva fila en la tabla de la fase.
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
                                                    <option value="{{ $member->user->nickname ?? $member->user->name ?? 'Sin nombre' }}">
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
                <form id="formCompletarFase{{ $phase->id }}" action="{{ route('fabricasoft.admin.projects.phase.complete.with.info.simple', $project->id) }}" method="POST" enctype="multipart/form-data">
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
                                                  rows="4" placeholder="Describe el trabajo completado en esta fase..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_notas{{ $phase->id }}" class="form-label">Notas</label>
                                        <textarea class="form-control" id="complete_notas{{ $phase->id }}" name="notas" 
                                                  rows="3" placeholder="Notas adicionales, observaciones..."></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_comentario{{ $phase->id }}" class="form-label">Comentario Adicional</label>
                                        <textarea class="form-control" id="complete_comentario{{ $phase->id }}" name="comentario_adicional" 
                                                  rows="2" placeholder="Comentario o contexto adicional..."></textarea>
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
                                        <label for="complete_documento{{ $phase->id }}" class="form-label">Documento</label>
                                        <input type="file" class="form-control" id="complete_documento{{ $phase->id }}" name="documento" 
                                               accept=".pdf,.doc,.docx,.txt">
                                        <div class="form-text">Sube un documento relacionado con la finalización de esta fase.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="complete_fecha_creacion{{ $phase->id }}" class="form-label">Fecha de Finalización</label>
                                        <input type="date" class="form-control" id="complete_fecha_creacion{{ $phase->id }}" name="fecha_creacion" 
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" onclick="enviarFormularioCompletarFase({{ $phase->id }})">
                            <i class="fas fa-check me-2"></i>Completar {{ $phase->phase_name }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modales individuales para editar información de cada fase -->
@foreach($project->phases->where('status', 'completed') as $completedPhase)
    <div class="modal fade" id="modalEditarInformacion{{ $completedPhase->id }}" tabindex="-1" aria-labelledby="modalEditarInformacionLabel{{ $completedPhase->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditarInformacionLabel{{ $completedPhase->id }}">
                        <i class="fas fa-edit me-2"></i>Editar Información de {{ $completedPhase->title }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditarInformacion{{ $completedPhase->id }}" action="{{ route('fabricasoft.admin.projects.phase.info.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="history_id" id="edit_history_id{{ $completedPhase->id }}">
                    <input type="hidden" name="phase_order" value="{{ $completedPhase->order }}">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Edita la información de la fase <strong>{{ $completedPhase->title }}</strong>. Los cambios se aplicarán inmediatamente.
                        </div>
                        
                        @if($completedPhase->order == 4)
                            <!-- Campos específicos para editar Fase 4 (Codificación) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_modulo{{ $completedPhase->id }}" class="form-label">Módulo *</label>
                                        <input type="text" class="form-control" id="edit_modulo{{ $completedPhase->id }}" name="modulo" 
                                               placeholder="Ej: Autenticación, Dashboard, API, etc." required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_desarrollador{{ $completedPhase->id }}" class="form-label">Desarrollador *</label>
                                        <select class="form-select" id="edit_desarrollador{{ $completedPhase->id }}" name="desarrollador" required>
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
                                        <label for="edit_fecha_desarrollo{{ $completedPhase->id }}" class="form-label">Fecha de Desarrollo *</label>
                                        <input type="date" class="form-control" id="edit_fecha_desarrollo{{ $completedPhase->id }}" name="fecha_desarrollo" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_nombre_commit{{ $completedPhase->id }}" class="form-label">Commit *</label>
                                        <input type="text" class="form-control" id="edit_nombre_commit{{ $completedPhase->id }}" name="nombre_commit" 
                                               placeholder="Ej: feat: implementa autenticación de usuarios" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_url_repositorio{{ $completedPhase->id }}" class="form-label">Repositorio *</label>
                                        <input type="url" class="form-control" id="edit_url_repositorio{{ $completedPhase->id }}" name="url_repositorio" 
                                               placeholder="https://github.com/usuario/repositorio" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_rama{{ $completedPhase->id }}" class="form-label">Rama (opcional)</label>
                                        <input type="text" class="form-control" id="edit_rama{{ $completedPhase->id }}" name="rama" 
                                               placeholder="Ej: develop, feature/auth, main">
                                    </div>
                                </div>
                            </div>
                        @elseif($completedPhase->order == 6)
                            <!-- Campos específicos para editar Fase 6 (Implementación) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_descripcion{{ $completedPhase->id }}" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="edit_descripcion{{ $completedPhase->id }}" name="descripcion" 
                                                  rows="4" placeholder="Describe la implementación realizada..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_usuario{{ $completedPhase->id }}" class="form-label">Usuario *</label>
                                        <input type="text" class="form-control" id="edit_usuario{{ $completedPhase->id }}" name="usuario" 
                                               placeholder="Usuario de acceso al sistema" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_contrasena{{ $completedPhase->id }}" class="form-label">Contraseña *</label>
                                        <input type="password" class="form-control" id="edit_contrasena{{ $completedPhase->id }}" name="contrasena" 
                                               placeholder="Contraseña de acceso al sistema" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    
                                    
                                    <div class="mb-3">
                                        <label for="edit_dominio{{ $completedPhase->id }}" class="form-label">Dominio *</label>
                                        <input type="text" class="form-control" id="edit_dominio{{ $completedPhase->id }}" name="dominio" 
                                               placeholder="sistema.ejemplo.com" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_url_completo{{ $completedPhase->id }}" class="form-label">URL Completo *</label>
                                        <input type="url" class="form-control" id="edit_url_completo{{ $completedPhase->id }}" name="url_completo" 
                                               placeholder="https://sistema.ejemplo.com/login" required>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Campos generales para editar otras fases -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_descripcion{{ $completedPhase->id }}" class="form-label">Descripción *</label>
                                        <textarea class="form-control" id="edit_descripcion{{ $completedPhase->id }}" name="descripcion" 
                                                  rows="4" placeholder="Describe la información de la fase..." required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_notas{{ $completedPhase->id }}" class="form-label">Notas</label>
                                        <textarea class="form-control" id="edit_notas{{ $completedPhase->id }}" name="notas" 
                                                  rows="3" placeholder="Notas adicionales, observaciones..."></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_comentario_adicional{{ $completedPhase->id }}" class="form-label">Comentario Adicional</label>
                                        <textarea class="form-control" id="edit_comentario_adicional{{ $completedPhase->id }}" name="comentario_adicional" 
                                                  rows="2" placeholder="Comentario o contexto adicional..."></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_enlace{{ $completedPhase->id }}" class="form-label">Enlace</label>
                                        <input type="url" class="form-control" id="edit_enlace{{ $completedPhase->id }}" name="enlace" 
                                               placeholder="https://example.com/documento">
                                        <div class="form-text">Enlace a documentación externa o recursos relacionados.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_fecha_creacion{{ $completedPhase->id }}" class="form-label">Fecha de Creación</label>
                                        <input type="date" class="form-control" id="edit_fecha_creacion{{ $completedPhase->id }}" name="fecha_creacion">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="edit_documento{{ $completedPhase->id }}" class="form-label">Nuevo Documento (opcional)</label>
                                        <input type="file" class="form-control" id="edit_documento{{ $completedPhase->id }}" name="documento" 
                                               accept=".pdf,.doc,.docx,.txt">
                                        <div class="form-text">Sube un nuevo documento para reemplazar el existente.</div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
@endforeach

<!-- Modal Genérico para Todas las Fases -->
<div class="modal fade" id="modalFase" tabindex="-1" aria-labelledby="modalFaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalFaseLabel">
                    <i class="fas fa-edit me-2"></i>Completar Fase
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formFase" action="{{ route('fabricasoft.admin.projects.phase.save', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="phase_id" name="phase_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Completa la información de esta fase. Esta información será fundamental para el desarrollo del proyecto.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phase_title" class="form-label">Título de la Fase *</label>
                                <input type="text" class="form-control" id="phase_title" name="phase_title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phase_description" class="form-label">Descripción *</label>
                                <textarea class="form-control" id="phase_description" name="phase_description" 
                                          rows="4" placeholder="Describe detalladamente el trabajo realizado en esta fase..." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phase_notes" class="form-label">Notas</label>
                                <textarea class="form-control" id="phase_notes" name="phase_notes" 
                                          rows="3" placeholder="Notas adicionales, observaciones, consideraciones especiales..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phase_link" class="form-label">Enlace</label>
                                <input type="url" class="form-control" id="phase_link" name="phase_link" 
                                       placeholder="https://example.com/documento">
                                <div class="form-text">Enlace a documentación externa, repositorio, o recursos relacionados.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phase_document" class="form-label">Documento</label>
                                <input type="file" class="form-control" id="phase_document" name="phase_document" 
                                       accept=".pdf,.doc,.docx,.txt">
                                <div class="form-text">Sube el documento relacionado con esta fase (PDF, DOC, DOCX, TXT).</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phase_status" class="form-label">Estado de la Fase *</label>
                                <select class="form-select" id="phase_status" name="phase_status" required>
                                    <option value="in_progress">En Progreso</option>
                                    <option value="completed">Completada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.border-left-primary {
    border-left: 4px solid #0d6efd !important;
}

.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}

.border-left-secondary {
    border-left: 4px solid #6c757d !important;
}

.card-header.bg-success {
    background-color: #28a745 !important;
}

.card-header.bg-primary {
    background-color: #0d6efd !important;
}

.modal-xl {
    max-width: 1140px;
}

.progress {
    border-radius: 0.5rem;
}

.progress-bar {
    border-radius: 0.5rem;
}

/* Estilos del Acordeón */
.accordion-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.accordion-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

/* Estilos específicos para fases completadas */
.accordion-item.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.accordion-item.border-left-success .accordion-button {
    background-color: #f8f9fa;
    color: #155724;
}

.accordion-item.border-left-success .accordion-button:not(.collapsed) {
    background-color: #d4edda;
    color: #155724;
    box-shadow: none;
}

.accordion-button {
    background-color: #f8f9fa;
    border: none;
    padding: 1rem 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
    background-color: #e9ecef;
    color: #2c3e50;
    box-shadow: none;
}

.accordion-button:hover {
    background-color: #e9ecef;
    color: #2c3e50;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.accordion-body {
    padding: 1.5rem;
    background-color: #ffffff;
}

/* Estilos para los botones en el header del acordeón */
.accordion-button .btn {
    margin-left: 0.5rem;
    white-space: nowrap;
}

.accordion-button .d-flex {
    align-items: center;
}

/* Mejorar la alineación de los elementos del header */
.accordion-button .d-flex.justify-content-between {
    width: 100%;
}

.accordion-button .d-flex.align-items-center {
    flex: 1;
}

/* Estilos para los badges */
.accordion-button .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    font-weight: 500;
    margin-right: 0.75rem;
}

/* Estilos para el título de la fase */
.accordion-button h6 {
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
}

/* Estilos para los badges y botones */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    font-weight: 500;
}

.btn-sm {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

/* Estilos para las tarjetas de detalles técnicos */
.card.bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6;
}

.card.bg-light .card-title {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Estilos para los enlaces */
.text-primary {
    color: #0d6efd !important;
    text-decoration: none;
}

.text-primary:hover {
    color: #0a58ca !important;
    text-decoration: underline;
}

/* Animaciones */
.accordion-collapse {
    transition: all 0.3s ease;
}

.accordion-button::after {
    transition: transform 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .accordion-button {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
    
    .accordion-body {
        padding: 1rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function mostrarModalAgregarDesarrollador() {
    const modal = new bootstrap.Modal(document.getElementById('modalAgregarDesarrollador'));
    modal.show();
}

function mostrarModalFase2() {
    const modal = new bootstrap.Modal(document.getElementById('modalFase2'));
    modal.show();
}

function mostrarModalFase(phaseId) {
    // Obtener información de la fase
    fetch(`/fabricasoft/admin/proyecto/{{ $project->id }}/fase/${phaseId}/info`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const phase = data.phase;
                
                // Llenar el modal con la información de la fase
                document.getElementById('phase_id').value = phase.id;
                document.getElementById('phase_title').value = phase.phase_name;
                document.getElementById('phase_description').value = phase.description || '';
                document.getElementById('phase_notes').value = phase.notes || '';
                document.getElementById('phase_link').value = phase.external_link || '';
                document.getElementById('phase_status').value = phase.status;
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('modalFase'));
                modal.show();
            } else {
                alert('Error al obtener información de la fase: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al obtener información de la fase.');
        });
}

function iniciarFase(phaseId) {
    if (confirm('¿Estás seguro de que quieres iniciar esta fase?')) {
        fetch(`/fabricasoft/admin/proyecto/{{ $project->id }}/fase/${phaseId}/iniciar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Fase iniciada exitosamente.');
                location.reload();
            } else {
                alert('Error al iniciar la fase: ' + data.message);
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Error al iniciar la fase.');
        });
    }
}

// Función para limpiar campos del modal de completar fase


function entregarProyecto(buttonElement, projectId) {
    // Verificar si el botón ya fue presionado (localStorage)
    const buttonState = localStorage.getItem('proyecto_entregado_' + projectId);
    if (buttonState === 'true') {
        alert('⚠️ ¡ATENCIÓN!\n\nEste proyecto YA FUE ENTREGADO.\n\nEl botón permanecerá deshabilitado permanentemente.\n\nSi necesitas ver el estado actualizado, recarga la página.');
        return;
    }
    
    // Verificar si el botón ya fue presionado en esta sesión
    if (buttonElement.getAttribute('data-clicked') === 'true') {
        alert('⚠️ ¡ATENCIÓN!\n\nEste proyecto ya está siendo procesado para entrega.\n\nPor favor, espera a que se complete la operación.');
        return;
    }
    
    // Marcar el botón como clickeado INMEDIATAMENTE
    buttonElement.setAttribute('data-clicked', 'true');
    
    // GUARDAR EN LOCALSTORAGE INMEDIATAMENTE
    localStorage.setItem('proyecto_entregado_' + projectId, 'true');
    
    // Deshabilitar el botón y cambiar su apariencia
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>PROCESANDO ENTREGA...';
    buttonElement.className = 'btn btn-warning btn-lg';
    buttonElement.title = 'Proyecto en proceso de entrega - NO PRESIONAR';
    
    // Mostrar mensaje de confirmación
    if (confirm('🚀 ¿ENTREGAR PROYECTO?\n\nEsta acción:\n✅ Marcará el proyecto como FINALIZADO\n✅ Liberará al SCRUM MASTER para nuevos proyectos\n✅ Liberará al EQUIPO DE DESARROLLO\n✅ NO se podrá deshacer\n\n¿Estás seguro de continuar?')) {
        
        // Cambiar a estado de envío
        buttonElement.innerHTML = '<i class="fas fa-paper-plane me-2"></i>ENVIANDO ENTREGA...';
        buttonElement.className = 'btn btn-info btn-lg';
        
        // Verificar CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token no encontrado');
            alert('❌ Error: Token CSRF no encontrado');
            // Mantener botón deshabilitado con mensaje de error
            buttonElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>ERROR - TOKEN CSRF';
            buttonElement.className = 'btn btn-danger btn-lg';
            buttonElement.title = 'Error: Token CSRF no encontrado';
            return;
        }
        
        // Enviar solicitud para entregar el proyecto
        fetch(`{{ route('fabricasoft.admin.projects.deliver', ':projectId') }}`.replace(':projectId', projectId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Content-Type': 'application/json',
            },
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                // ÉXITO - Cambiar botón a estado final
                buttonElement.innerHTML = '<i class="fas fa-check-circle me-2"></i>¡PROYECTO ENTREGADO!';
                buttonElement.className = 'btn btn-success btn-lg';
                buttonElement.title = 'Proyecto entregado exitosamente';
                
                // Mostrar mensaje de éxito
                const successMessage = `
                    🎉 ¡PROYECTO ENTREGADO EXITOSAMENTE! 🎉
                    
                    📋 Estado del Proyecto: COMPLETADO Y ENTREGADO
                    ✅ Fases: Todas marcadas como completadas
                    ✅ Equipo: Todos los miembros liberados
                    👑 Scrum Master: LIBRE para nuevos proyectos
                    
                    🚀 El Scrum Master ya puede ser asignado a nuevos proyectos Scrum.
                    🎯 El equipo de desarrollo está disponible para nuevas asignaciones.
                `;
                
                alert(successMessage);
                
                // Recargar la página después de 2 segundos
                setTimeout(() => {
                    location.reload();
                }, 2000);
                
            } else {
                // ERROR del servidor
                buttonElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>ERROR - NO SE PUDO ENTREGAR';
                buttonElement.className = 'btn btn-danger btn-lg';
                buttonElement.title = 'Error del servidor al entregar proyecto';
                alert('❌ Error al entregar el proyecto: ' + (data.message || 'Error desconocido'));
            }
        }).catch(error => {
            // ERROR de red o JavaScript
            console.error('Error al entregar proyecto:', error);
            buttonElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>ERROR DE CONEXIÓN';
            buttonElement.className = 'btn btn-danger btn-lg';
            buttonElement.title = 'Error de conexión al entregar proyecto';
            alert('❌ Error de conexión al entregar el proyecto: ' + error.message);
        });
        
    } else {
        // USUARIO CANCELÓ - Mantener botón deshabilitado
        buttonElement.innerHTML = '<i class="fas fa-times me-2"></i>ENTREGA CANCELADA';
        buttonElement.className = 'btn btn-secondary btn-lg';
        buttonElement.title = 'Entrega cancelada por el usuario - NO se puede volver a intentar';
        
        // Mostrar mensaje de cancelación
        alert('⚠️ Entrega cancelada\n\nEl botón permanecerá deshabilitado permanentemente.\n\nSi necesitas intentar nuevamente, recarga la página.');
    }
}

function completarFase(phaseId) {
    console.log('completarFase llamado con phaseId:', phaseId);
    
    // Mostrar el modal específico de la fase
    const modalElement = document.getElementById('modalCompletarFase' + phaseId);
    
    if (!modalElement) {
        console.error('Modal modalCompletarFase' + phaseId + ' no encontrado');
        alert('Error: Modal no encontrado para la fase ' + phaseId);
        return;
    }
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

function enviarFormularioCompletarFase(phaseId) {
    console.log('enviarFormularioCompletarFase llamado con phaseId:', phaseId);
    
    // Obtener el formulario
    const formulario = document.getElementById('formCompletarFase' + phaseId);
    if (!formulario) {
        console.error('Formulario no encontrado para fase:', phaseId);
        alert('Error: Formulario no encontrado');
        return;
    }
    
    // Crear FormData del formulario
    const formData = new FormData(formulario);
    const phaseOrder = formData.get('phase_order');
    
    console.log('Enviando formulario de completar fase para phaseId:', phaseId);
    console.log('FormData contents:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Validar campos según el tipo de fase
    if (phaseOrder == '4') {
        // Validar campos específicos de Fase 4
        if (!formData.get('desarrollador') || !formData.get('modulo') || !formData.get('fecha_desarrollo') || 
            !formData.get('nombre_commit') || !formData.get('url_repositorio')) {
            alert('Todos los campos de la Fase 4 son obligatorios (excepto Rama).');
            return;
        }
        console.log('Validación de Fase 4 exitosa');
    } else if (phaseOrder == '6') {
        // Validar campos específicos de Fase 6
        if (!formData.get('descripcion') || !formData.get('usuario') || !formData.get('contrasena') || 
            !formData.get('dominio') || !formData.get('url_completo')) {
            alert('Todos los campos de la Fase 6 son obligatorios.');
            return;
        }
        console.log('Validación de Fase 6 exitosa');
    } else {
        // Validar campos generales para otras fases
        if (!formData.get('descripcion')) {
            alert('La descripción es obligatoria para completar la fase.');
            return;
        }
        console.log('Validación de fase general exitosa');
    }
    
    // Verificar CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token no encontrado');
        alert('Error: Token CSRF no encontrado');
        return;
    }
    
    console.log('Enviando formulario de completar fase a:', formulario.action);
    
    // Enviar formulario via AJAX
    fetch(formulario.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
        },
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensaje especial si el proyecto está completado
            if (data.project_completed) {
                alert('🎉 ¡PROYECTO COMPLETADO EXITOSAMENTE! 🎉\n\n' + data.message + '\n\nEl Scrum Master ha sido liberado para el próximo proyecto.');
            } else {
                alert('Fase completada exitosamente con la información proporcionada.');
            }
            
            // Cerrar modal
            const modal = document.getElementById('modalCompletarFase' + phaseId);
            if (modal) {
                const bootstrapModal = bootstrap.Modal.getInstance(modal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
            }
            
            // Recargar página
            location.reload();
        } else {
            alert('Error al completar la fase: ' + (data.message || 'Error desconocido'));
        }
    }).catch(error => {
        console.error('Error en fetch de completar fase:', error);
        alert('Error al completar la fase: ' + error.message);
    });
}



function removerMiembro(memberId) {
    if (confirm('¿Estás seguro de que quieres remover este miembro del equipo?')) {
        fetch(`/fabricasoft/admin/proyecto/{{ $project->id }}/remover-miembro/${memberId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }).catch(error => {
            console.error('Error en la petición:', error);
            alert('Error al remover el miembro del equipo.');
        });
    }
}

function seguirSiguienteFase() {
    if (confirm('¿Estás seguro de que quieres avanzar a la siguiente fase? Esto marcará la Fase 2 como completada.')) {
        // Aquí puedes implementar la lógica para avanzar a la siguiente fase
        alert('Función para avanzar a la siguiente fase - Implementar según necesidades');
    }
}

function avanzarSiguienteFase(nextPhaseId) {
    if (confirm('¿Estás seguro de que quieres avanzar a la siguiente fase?')) {
        // Primero iniciar la fase
        fetch(`/fabricasoft/admin/proyecto/{{ $project->id }}/fase/${nextPhaseId}/iniciar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Siguiente fase iniciada exitosamente. Ahora puedes completarla.');
                location.reload();
            } else {
                alert('Error al iniciar la siguiente fase: ' + data.message);
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Error al iniciar la siguiente fase.');
        });
    }
}

function agregarInformacionFase(phaseId) {
    console.log('agregarInformacionFase llamada con phaseId:', phaseId);
    
    try {
        // Mostrar el modal específico de la fase
        const modalElement = document.getElementById('modalAgregarInformacion' + phaseId);
        
        if (!modalElement) {
            console.error('Modal modalAgregarInformacion' + phaseId + ' no encontrado');
            alert('Error: Modal no encontrado para la fase ' + phaseId);
            return;
        }
        
        // Obtener el orden de la fase para determinar qué campos limpiar
        const phaseOrderElement = document.querySelector('#modalAgregarInformacion' + phaseId + ' input[name="phase_order"]');
        const phaseOrder = phaseOrderElement ? parseInt(phaseOrderElement.value) : 0;
        
        if (phaseOrder === 4) {
            // Limpiar campos específicos de desarrollo de software para Fase 4
            const camposDesarrollo = ['info_modulo', 'info_desarrollador', 'info_fecha_desarrollo', 'info_nombre_commit', 'info_url_repositorio', 'info_rama'];
            camposDesarrollo.forEach(campoId => {
                const elemento = document.getElementById(campoId + phaseId);
                if (elemento) {
                    if (campoId === 'info_fecha_desarrollo') {
                        elemento.value = new Date().toISOString().split('T')[0]; // Fecha actual
                    } else {
                        elemento.value = '';
                    }
                } else {
                    console.warn('Campo no encontrado:', campoId + phaseId);
                }
            });
        } else {
            // Limpiar campos generales para otras fases
            const camposGenerales = ['info_descripcion', 'info_notas', 'info_fecha_creacion', 'info_enlace', 'info_comentario_adicional'];
            camposGenerales.forEach(campoId => {
                const elemento = document.getElementById(campoId + phaseId);
                if (elemento) {
                    if (campoId === 'info_fecha_creacion') {
                        elemento.value = new Date().toISOString().split('T')[0]; // Fecha actual
                    } else if (campoId === 'info_documento') {
                        // No limpiar campos de archivo
                    } else {
                        elemento.value = '';
                    }
                } else {
                    console.warn('Campo no encontrado:', campoId + phaseId);
                }
            });
        }
        
        // Mostrar el modal usando Bootstrap
        console.log('Intentando mostrar modal para fase ' + phaseId + ' (Orden: ' + phaseOrder + ')...');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('Modal mostrado con Bootstrap para fase ' + phaseId);
        
    } catch (error) {
        console.error('Error en agregarInformacionFase:', error);
        alert('Error al abrir el modal: ' + error.message);
    }
}

function editarInformacionFase(historyId) {
    console.log('editarInformacionFase llamada con historyId:', historyId);
    
    try {
        // Obtener la información de la fase para editar
        fetch(`/fabricasoft/admin/proyecto/{{ $project->id }}/fase/informacion/${historyId}/info`)
            .then(response => {
                console.log('Respuesta del servidor:', response);
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos del servidor:', data);
                
                if (data.success) {
                    // Obtener el ID de la fase para determinar qué modal abrir
                    const phaseId = data.phase.phase_id;
                    console.log('Phase ID obtenido:', phaseId);
                    console.log('Phase Order obtenido:', data.phase.phase_order);
                    
                    // Llenar el modal de edición con la información existente
                    const historyIdElement = document.getElementById('edit_history_id' + phaseId);
                    if (historyIdElement) {
                        historyIdElement.value = historyId;
                        console.log('Campo history_id llenado con:', historyId);
                    } else {
                        console.error('Campo history_id no encontrado para fase:', phaseId);
                    }
                    
                    // Determinar si es Fase 4 basándose en el orden de la fase
                    const phaseOrder = data.phase.phase_order || 0;
                    console.log('Phase Order:', phaseOrder);
                    
                    if (phaseOrder == 4) {
                        console.log('Procesando como Fase 4 (Codificación)');
                        // Llenar campos específicos de Fase 4 (Codificación)
                        fillPhase4EditFields(phaseId, data.phase);
                    } else if (phaseOrder == 6) {
                        console.log('Procesando como Fase 6 (Implementación)');
                        // Llenar campos específicos de Fase 6 (Implementación)
                        fillPhase6EditFields(phaseId, data.phase);
                    } else {
                        console.log('Procesando como fase general');
                        // Llenar campos generales para otras fases
                        fillGeneralEditFields(phaseId, data.phase);
                    }
                    
                    // Mostrar el modal de edición específico de la fase
                    const modalElement = document.getElementById('modalEditarInformacion' + phaseId);
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                        console.log('Modal de edición abierto para fase:', phaseId);
                    } else {
                        console.error('Modal de edición no encontrado para fase:', phaseId);
                        alert('Error: Modal de edición no encontrado para la fase ' + phaseId);
                    }
                } else {
                    console.error('Error en la respuesta del servidor:', data.message);
                    alert('Error al obtener información de la fase: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                alert('Error al obtener información de la fase para editar.');
            });
    } catch (error) {
        console.error('Error en editarInformacionFase:', error);
        alert('Error al abrir el modal de edición: ' + error.message);
    }
}



// Función para llenar campos de edición de Fase 4
function fillPhase4EditFields(phaseId, phaseData) {
    console.log('Llenando campos de Fase 4 para edición');
    console.log('Datos recibidos:', phaseData);
    
    // Extraer información de los campos específicos de Fase 4
    let modulo = '';
    let commit = '';
    let desarrollador = '';
    let rama = '';
    let fechaDesarrollo = '';
    
    // Extraer módulo y commit del campo description
    if (phaseData.description) {
        const descParts = phaseData.description.split(' | ');
        modulo = descParts[0] ? descParts[0].replace('Módulo: ', '') : '';
        commit = descParts[1] ? descParts[1].replace('Commit: ', '') : '';
    }
    
    // Extraer desarrollador y rama del campo notes
    if (phaseData.notes) {
        const notesParts = phaseData.notes.split(' | ');
        // Buscar el desarrollador (puede estar en diferentes posiciones)
        for (let part of notesParts) {
            if (part.startsWith('Desarrollador: ')) {
                desarrollador = part.replace('Desarrollador: ', '');
                break;
            }
        }
        // Buscar la rama (puede estar en diferentes posiciones)
        for (let part of notesParts) {
            if (part.startsWith('Rama: ')) {
                rama = part.replace('Rama: ', '');
                break;
            }
        }
    }
    
    // Extraer fecha de desarrollo del campo additional_comment
    if (phaseData.additional_comment) {
        fechaDesarrollo = phaseData.additional_comment.replace('Fecha de desarrollo: ', '');
    }
    
    console.log('Datos extraídos:', { modulo, commit, desarrollador, rama, fechaDesarrollo });
    
    // Llenar campos del modal
    const campos = {
        'edit_modulo': modulo,
        'edit_desarrollador': desarrollador,
        'edit_fecha_desarrollo': fechaDesarrollo,
        'edit_nombre_commit': commit,
        'edit_url_repositorio': phaseData.external_link || '',
        'edit_rama': rama
    };
    
    console.log('Campos a llenar:', campos);
    console.log('Phase ID:', phaseId);
    
    Object.keys(campos).forEach(campoId => {
        const campoCompleto = campoId + phaseId;
        const elemento = document.getElementById(campoCompleto);
        console.log(`Buscando campo: ${campoCompleto}`);
        
        if (elemento) {
            elemento.value = campos[campoId];
            console.log(`Campo ${campoCompleto} llenado con:`, campos[campoId]);
            
            // Verificar que el valor se estableció correctamente
            setTimeout(() => {
                console.log(`Verificación - Campo ${campoCompleto} tiene valor:`, elemento.value);
            }, 100);
        } else {
            console.error('Campo no encontrado:', campoCompleto);
            console.error('Elementos disponibles con edit_modulo:', document.querySelectorAll('[id^="edit_modulo"]'));
        }
    });
}

// Función para llenar campos de edición de Fase 6
function fillPhase6EditFields(phaseId, phaseData) {
    console.log('Llenando campos de Fase 6 para edición');
    console.log('Datos recibidos:', phaseData);
    
    // Extraer información de los campos específicos de Fase 6
    let descripcion = '';
    let usuario = '';
    let contrasena = '';
    let url = '';
    let dominio = '';
    let urlCompleto = '';
    
    // Extraer descripción del campo description
    if (phaseData.description) {
        descripcion = phaseData.description;
    }
    
    // Extraer usuario y contraseña del campo notes
    if (phaseData.notes) {
        const notesParts = phaseData.notes.split(' | ');
        // Buscar el usuario
        for (let part of notesParts) {
            if (part.startsWith('Usuario: ')) {
                usuario = part.replace('Usuario: ', '');
                break;
            }
        }
        // Buscar la contraseña
        for (let part of notesParts) {
            if (part.startsWith('Contraseña: ')) {
                contrasena = part.replace('Contraseña: ', '');
                break;
            }
        }
    }
    
    // Extraer URL, dominio y URL completo del campo external_link y additional_comment
    if (phaseData.external_link) {
        url = phaseData.external_link;
    }
    
    if (phaseData.additional_comment) {
        const commentParts = phaseData.additional_comment.split(' | ');
        // Buscar el dominio
        for (let part of commentParts) {
            if (part.startsWith('Dominio: ')) {
                dominio = part.replace('Dominio: ', '');
                break;
            }
        }
        // Buscar la URL completa
        for (let part of commentParts) {
            if (part.startsWith('URL Completo: ')) {
                urlCompleto = part.replace('URL Completo: ', '');
                break;
            }
        }
    }
    
    console.log('Datos extraídos:', { descripcion, usuario, contrasena, url, dominio, urlCompleto });
    
    // Llenar campos del modal
    const campos = {
        'edit_descripcion': descripcion,
        'edit_usuario': usuario,
        'edit_contrasena': contrasena,
        'edit_dominio': dominio,
        'edit_url_completo': urlCompleto
    };
    
    console.log('Campos a llenar:', campos);
    console.log('Phase ID:', phaseId);
    
    Object.keys(campos).forEach(campoId => {
        const campoCompleto = campoId + phaseId;
        const elemento = document.getElementById(campoCompleto);
        console.log(`Buscando campo: ${campoCompleto}`);
        
        if (elemento) {
            elemento.value = campos[campoId];
            console.log(`Campo ${campoCompleto} llenado con:`, campos[campoId]);
            
            // Verificar que el valor se estableció correctamente
            setTimeout(() => {
                console.log(`Verificación - Campo ${campoCompleto} tiene valor:`, elemento.value);
            }, 100);
        } else {
            console.error('Campo no encontrado:', campoCompleto);
            console.error('Elementos disponibles con edit_descripcion:', document.querySelectorAll('[id^="edit_descripcion"]'));
        }
    });
}

// Función para llenar campos generales de edición
function fillGeneralEditFields(phaseId, phaseData) {
    console.log('Llenando campos generales para edición');
    
    // Llenar campos del modal
    const campos = {
        'edit_descripcion': phaseData.description || '',
        'edit_notas': phaseData.notes || '',
        'edit_enlace': phaseData.external_link || '',
        'edit_comentario_adicional': phaseData.additional_comment || '',
        'edit_fecha_creacion': phaseData.start_date || ''
    };
    
    Object.keys(campos).forEach(campoId => {
        const elemento = document.getElementById(campoId + phaseId);
        if (elemento) {
            elemento.value = campos[campoId];
        } else {
            console.warn('Campo no encontrado:', campoId + phaseId);
        }
    });
}

function eliminarInformacionFase(historyId) {
    if (confirm('¿Estás seguro de que quieres eliminar esta información? Esta acción no se puede deshacer.')) {
        console.log('eliminarInformacionFase llamada con historyId:', historyId);
        
        try {
            // Enviar solicitud para eliminar el elemento del historial
            fetch(`/fabricasoft/admin/proyecto/{{ $project->id }}/fase/informacion/${historyId}/eliminar`, {
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


window.agregarInformacionFase = agregarInformacionFase;
window.completarFase = completarFase;
window.enviarFormularioCompletarFase = enviarFormularioCompletarFase;
window.entregarProyecto = entregarProyecto;

// Event listeners cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, configurando event listeners...');
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined') {
        console.error('ERROR: Bootstrap no está disponible. Los modales no funcionarán.');
        alert('Error: Bootstrap no está disponible. Los modales no funcionarán.');
    } else {
        console.log('Bootstrap disponible:', bootstrap);
    }
    
    // Verificar que los modales existan
    const modales = [
        'modalAgregarDesarrollador',
        'modalFase2',
        'modalAgregarInformacion',
        'modalCompletarFase',
        'modalEditarInformacion',
        'modalFase'
    ];
    
    modales.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            console.log(`Modal ${modalId} encontrado`);
        } else {
            console.error(`Modal ${modalId} NO encontrado`);
        }
    });
    
    // Event listener para el formulario de Fase 2
    const formFase2 = document.getElementById('formFase2');
    if (formFase2) {
        formFase2.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Fase 2 guardada exitosamente.');
                    bootstrap.Modal.getInstance(document.getElementById('modalFase2')).hide();
                    location.reload();
                } else {
                    alert('Error al guardar la fase: ' + data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la fase.');
            });
        });
    }
    
    // Event listener para el formulario genérico de fases
    const formFase = document.getElementById('formFase');
    if (formFase) {
        formFase.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Fase guardada exitosamente.');
                    bootstrap.Modal.getInstance(document.getElementById('modalFase')).hide();
                    location.reload();
                } else {
                    alert('Error al guardar la fase: ' + data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la fase.');
            });
        });
    }
    
    // Event listeners para todos los formularios de agregar información
    @foreach($project->phases->where('status', 'completed') as $completedPhase)
    const formAgregarInformacion{{ $completedPhase->id }} = document.getElementById('formAgregarInformacion{{ $completedPhase->id }}');
    if (formAgregarInformacion{{ $completedPhase->id }}) {
        console.log('Formulario de agregar información para fase {{ $completedPhase->id }} encontrado');
        
        formAgregarInformacion{{ $completedPhase->id }}.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Formulario enviado para fase {{ $completedPhase->id }}');
            
            const formData = new FormData(this);
            
            // Log de los datos del formulario
            console.log('Datos del formulario:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Validar campos según el tipo de fase
            if ({{ $completedPhase->order }} == 4) {
                // Validar campos específicos de Fase 4
                if (!formData.get('modulo') || !formData.get('desarrollador') || !formData.get('fecha_desarrollo') || 
                    !formData.get('nombre_commit') || !formData.get('url_repositorio')) {
                    alert('Todos los campos de la Fase 4 son obligatorios (excepto Rama).');
                    return;
                }
                console.log('Validación de Fase 4 exitosa');
            } else if ({{ $completedPhase->order }} == 6) {
                // Validar campos específicos de Fase 6
                if (!formData.get('descripcion') || !formData.get('usuario') || !formData.get('contrasena') || 
                    !formData.get('dominio') || !formData.get('url_completo')) {
                    alert('Todos los campos de la Fase 6 son obligatorios.');
                    return;
                }
                console.log('Validación de Fase 6 exitosa');
            } else {
                // Validar campos generales para otras fases
                if (!formData.get('descripcion')) {
                    alert('La descripción es obligatoria para agregar información.');
                    return;
                }
                console.log('Validación de fase general exitosa');
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
                    alert('Información agregada exitosamente a la fase {{ $completedPhase->title }}.');
                    
                    // Cerrar modal
                    const modal = document.getElementById('modalAgregarInformacion{{ $completedPhase->id }}');
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
        console.error('Formulario de agregar información para fase {{ $completedPhase->id }} NO encontrado');
    }
    @endforeach
    
    // Event listeners para todos los formularios de editar información
    @foreach($project->phases->where('status', 'completed') as $completedPhase)
    const formEditarInformacion{{ $completedPhase->id }} = document.getElementById('formEditarInformacion{{ $completedPhase->id }}');
    if (formEditarInformacion{{ $completedPhase->id }}) {
        console.log('Formulario de editar información para fase {{ $completedPhase->id }} encontrado');
        
        formEditarInformacion{{ $completedPhase->id }}.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Formulario de edición enviado para fase {{ $completedPhase->id }}');
            
            const formData = new FormData(this);
            
            // Log de los datos del formulario
            console.log('Datos del formulario de edición:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Validar campos según el tipo de fase
            if ({{ $completedPhase->order }} == 4) {
                // Validar campos específicos de Fase 4
                if (!formData.get('modulo') || !formData.get('desarrollador') || !formData.get('fecha_desarrollo') || 
                    !formData.get('nombre_commit') || !formData.get('url_repositorio')) {
                    alert('Todos los campos de la Fase 4 son obligatorios (excepto Rama).');
                    return;
                }
                console.log('Validación de Fase 4 exitosa');
            } else if ({{ $completedPhase->order }} == 6) {
                // Validar campos específicos de Fase 6
                if (!formData.get('descripcion') || !formData.get('usuario') || !formData.get('contrasena') || 
                    !formData.get('dominio') || !formData.get('url_completo')) {
                    alert('Todos los campos de la Fase 6 son obligatorios.');
                    return;
                }
                console.log('Validación de Fase 6 exitosa');
            } else {
                // Validar campos generales para otras fases
                if (!formData.get('descripcion')) {
                    alert('La descripción es obligatoria para editar información.');
                    return;
                }
                console.log('Validación de fase general exitosa');
            }
            
            // Log adicional para verificar campos específicos de Fase 4
            if ({{ $completedPhase->order }} == 4) {
                console.log('Verificando campos de Fase 4:');
                console.log('Módulo:', document.getElementById('edit_modulo{{ $completedPhase->id }}').value);
                console.log('Desarrollador:', document.getElementById('edit_desarrollador{{ $completedPhase->id }}').value);
                console.log('Fecha Desarrollo:', document.getElementById('edit_fecha_desarrollo{{ $completedPhase->id }}').value);
                console.log('Commit:', document.getElementById('edit_nombre_commit{{ $completedPhase->id }}').value);
                console.log('Repositorio:', document.getElementById('edit_url_repositorio{{ $completedPhase->id }}').value);
                console.log('Rama:', document.getElementById('edit_rama{{ $completedPhase->id }}').value);
            }
            
            // Log adicional para verificar campos específicos de Fase 6
            if ({{ $completedPhase->order }} == 6) {
                console.log('Verificando campos de Fase 6:');
                console.log('Descripción:', document.getElementById('edit_descripcion{{ $completedPhase->id }}').value);
                console.log('Usuario:', document.getElementById('edit_usuario{{ $completedPhase->id }}').value);
                console.log('Contraseña:', document.getElementById('edit_contrasena{{ $completedPhase->id }}').value);
                console.log('Dominio:', document.getElementById('edit_dominio{{ $completedPhase->id }}').value);
                console.log('URL Completo:', document.getElementById('edit_url_completo{{ $completedPhase->id }}').value);
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
                console.log('Respuesta de edición recibida:', response.status, response.statusText);
                return response.json();
            }).then(data => {
                console.log('Datos de respuesta de edición:', data);
                
                if (data.success) {
                    alert('Información de la fase {{ $completedPhase->title }} actualizada exitosamente.');
                    
                    // Cerrar modal
                    const modal = document.getElementById('modalEditarInformacion{{ $completedPhase->id }}');
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
                console.error('Error en fetch de edición:', error);
                alert('Error al actualizar la información de la fase: ' + error.message);
            });
        });
    } else {
        console.error('Formulario de editar información para fase {{ $completedPhase->id }} NO encontrado');
    }
    @endforeach
    
    // Event listeners para todos los formularios de completar fase
    @foreach($project->phases->where('status', '!=', 'completed') as $phase)
    console.log('Configurando event listener para fase {{ $phase->id }}');
    const formCompletarFase{{ $phase->id }} = document.getElementById('formCompletarFase{{ $phase->id }}');
    console.log('Formulario encontrado para fase {{ $phase->id }}:', formCompletarFase{{ $phase->id }});
    
    if (formCompletarFase{{ $phase->id }}) {
        console.log('Formulario de completar fase {{ $phase->id }} encontrado, agregando event listener...');
        
        formCompletarFase{{ $phase->id }}.addEventListener('submit', function(e) {
            console.log('Evento submit capturado para fase {{ $phase->id }}');
            e.preventDefault();
            console.log('Formulario de completar fase {{ $phase->id }} enviado');
            
            const formData = new FormData(this);
            const phaseId = formData.get('phase_id');
            const phaseOrder = formData.get('phase_order');
            
            console.log('Enviando formulario de completar fase para phaseId:', phaseId);
            console.log('FormData contents:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Validar campos según el tipo de fase
            if (phaseOrder == '4') {
                // Validar campos específicos de Fase 4
                if (!formData.get('desarrollador') || !formData.get('modulo') || !formData.get('fecha_desarrollo') || 
                    !formData.get('nombre_commit') || !formData.get('url_repositorio')) {
                    alert('Todos los campos de la Fase 4 son obligatorios (excepto Rama).');
                    return;
                }
                console.log('Validación de Fase 4 exitosa');
            } else if (phaseOrder == '6') {
                // Validar campos específicos de Fase 6
                if (!formData.get('descripcion') || !formData.get('usuario') || !formData.get('contrasena') || 
                    !formData.get('dominio') || !formData.get('url_completo')) {
                    alert('Todos los campos de la Fase 6 son obligatorios.');
                    return;
                }
                console.log('Validación de Fase 6 exitosa');
            } else {
                // Validar campos generales para otras fases
                if (!formData.get('descripcion')) {
                    alert('La descripción es obligatoria para completar la fase.');
                    return;
                }
                console.log('Validación de fase general exitosa');
            }
            
            // Validar que phaseId esté presente
            if (!phaseId) {
                alert('Error: No se pudo obtener el ID de la fase. Por favor, inténtalo de nuevo.');
                return;
            }
            
            // Verificar CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token no encontrado');
                alert('Error: Token CSRF no encontrado');
                return;
            }
            
            console.log('Enviando formulario de completar fase a:', this.action);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                },
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Fase completada exitosamente con la información proporcionada.');
                    
                    // Cerrar modal
                    const modal = document.getElementById('modalCompletarFase{{ $phase->id }}');
                    if (modal) {
                        const bootstrapModal = bootstrap.Modal.getInstance(modal);
                        if (bootstrapModal) {
                            bootstrapModal.hide();
                        }
                    }
                    
                    // Recargar página
                    location.reload();
                } else {
                    alert('Error al completar la fase: ' + (data.message || 'Error desconocido'));
                }
            }).catch(error => {
                console.error('Error en fetch de completar fase:', error);
                alert('Error al completar la fase: ' + error.message);
            });
        });
        console.log('Event listener agregado exitosamente para fase {{ $phase->id }}');
    } else {
        console.error('Formulario de completar fase {{ $phase->id }} NO encontrado');
    }
    @endforeach
    
    console.log('Event listeners configurados correctamente');
});

// Función para alternar la visibilidad de la contraseña
function togglePasswordVisibility(button) {
    const input = button.previousElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
        button.title = 'Ocultar contraseña';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
        button.title = 'Mostrar contraseña';
    }
}
</script>
@endpush
