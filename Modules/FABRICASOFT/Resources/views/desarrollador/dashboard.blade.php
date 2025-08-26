@extends('fabricasoft::layouts.app')
@section('title', 'Dashboard Desarrollador - FABRICASOFT')
@section('content')

<!-- Header del Dashboard -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-laptop-code me-2" style="color: var(--sena-green);"></i>
            Dashboard Desarrollador
        </h2>
        <p class="text-muted mb-0">Bienvenido, {{ Auth::user()->name }} - Panel de control para gestión de proyectos y desarrollo</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('fabricasoft.desarrollador.projects') }}" class="btn btn-sena">
            <i class="fas fa-project-diagram me-2"></i>Ver Todos los Proyectos
        </a>
    </div>
</div>

<!-- Tarjetas de Estadísticas Principales -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-gradient-primary">
            <div class="stats-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stats-content">
                <div class="stats-number">{{ $stats['total_projects'] }}</div>
                <div class="stats-label">Proyectos Asignados</div>
                <div class="stats-subtitle">
                    <span class="badge bg-success">{{ $stats['active_projects'] }} Activos</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-gradient-success">
            <div class="stats-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stats-content">
                <div class="stats-number">{{ $stats['total_phases'] }}</div>
                <div class="stats-label">Total de Fases</div>
                <div class="stats-subtitle">
                    <span class="badge bg-info">{{ $stats['completed_phases'] }} Completadas</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-gradient-warning">
            <div class="stats-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-content">
                <div class="stats-number">{{ $stats['in_progress_phases'] }}</div>
                <div class="stats-label">Fases en Progreso</div>
                <div class="stats-subtitle">
                    <span class="badge bg-warning">En Desarrollo</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card bg-gradient-info">
            <div class="stats-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stats-content">
                <div class="stats-number">{{ $stats['overall_progress'] }}%</div>
                <div class="stats-label">Progreso General</div>
                <div class="stats-subtitle">
                    <span class="badge bg-primary">Completado</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenido Principal -->
<div class="row">
    <!-- Proyectos Destacados -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>
                    Proyectos Destacados
                </h5>
            </div>
            <div class="card-body">
                @if($topProjects->count() > 0)
                    <div class="row">
                        @foreach($topProjects as $project)
                            @php
                                $totalPhases = $project->phases->count();
                                $completedPhases = $project->phases->where('status', 'completed')->count();
                                $progress = $totalPhases > 0 ? round(($completedPhases / $totalPhases) * 100, 1) : 0;
                                $statusColor = $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'primary' : 'secondary');
                            @endphp
                            
                            <div class="col-md-6 mb-3">
                                <div class="project-card h-100">
                                    <div class="project-header">
                                        <div class="project-icon bg-{{ $statusColor }}">
                                            <i class="fas fa-code"></i>
                                        </div>
                                        <div class="project-info">
                                            <h6 class="project-title">{{ $project->project_name }}</h6>
                                            <small class="text-muted">
                                                @if($project->preregistration)
                                                    {{ $project->preregistration->organization ?: 'Cliente Privado' }}
                                                @else
                                                    Cliente no especificado
                                                @endif
                                            </small>
                                        </div>
                                        <div class="project-status">
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ ucfirst($project->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="project-progress">
                                        <div class="d-flex justify-content-between mb-2">
                                            <small>Progreso del Proyecto</small>
                                            <small><strong>{{ $progress }}%</strong></small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $statusColor }}" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <small class="text-muted">Fases:</small>
                                            <small class="text-muted">{{ $completedPhases }}/{{ $totalPhases }} completadas</small>
                                        </div>
                                    </div>
                                    
                                    <div class="project-actions">
                                        <a href="{{ route('fabricasoft.desarrollador.projects.show', $project->id) }}" 
                                           class="btn btn-sena btn-sm flex-fill">
                                            <i class="fas fa-eye me-1"></i>Ver Proyecto
                                        </a>
                                        <button class="btn btn-outline-sena btn-sm" onclick="verFases({{ $project->id }})">
                                            <i class="fas fa-tasks"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay proyectos asignados</h5>
                        <p class="text-muted">El Scrum Master te asignará proyectos próximamente.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Actividad Reciente y Acciones Rápidas -->
    <div class="col-md-4 mb-4">
        <!-- Actividad Reciente -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-gradient-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Actividad Reciente
                </h6>
            </div>
            <div class="card-body">
                @if($recentActivity->count() > 0)
                    <div class="activity-timeline">
                        @foreach($recentActivity->take(5) as $activity)
                            <div class="activity-item">
                                <div class="activity-icon bg-primary">
                                    <i class="{{ $activity->info_type_icon ?? 'fas fa-edit' }}"></i>
                                </div>
                                <div class="activity-content">
                                    <p class="activity-text mb-1">
                                        <strong>{{ Str::limit($activity->content, 40) }}</strong>
                                    </p>
                                    <small class="text-muted">
                                        {{ $activity->project->project_name ?? 'Proyecto' }} - 
                                        {{ $activity->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No hay actividad reciente</p>
                    </div>
                @endif
            </div>
        </div>
        

    </div>
</div>



@endsection

@push('styles')
<style>
/* Estilos para las tarjetas de estadísticas */
.stats-card {
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: rotate(45deg);
}

.stats-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-label {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.stats-subtitle {
    margin-top: 0.5rem;
}

/* Gradientes de colores */
.bg-gradient-primary { background: linear-gradient(135deg, #007bff, #0056b3); }
.bg-gradient-success { background: linear-gradient(135deg, #28a745, #1e7e34); }
.bg-gradient-warning { background: linear-gradient(135deg, #ffc107, #e0a800); }
.bg-gradient-info { background: linear-gradient(135deg, #17a2b8, #138496); }
.bg-gradient-danger { background: linear-gradient(135deg, #dc3545, #c82333); }
.bg-gradient-secondary { background: linear-gradient(135deg, #6c757d, #495057); }
.bg-gradient-dark { background: linear-gradient(135deg, #343a40, #212529); }

/* Tarjetas de proyectos */
.project-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    background: white;
}

.project-card:hover {
    border-color: var(--sena-green);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.project-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.project-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.2rem;
    color: white;
}

.project-info {
    flex: 1;
}

.project-title {
    margin: 0;
    font-weight: 600;
}

.project-status {
    margin-left: auto;
}

.project-progress {
    margin-bottom: 1rem;
}

.project-actions {
    display: flex;
    gap: 0.5rem;
}

/* Timeline de actividad */
.activity-timeline {
    position: relative;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f8f9fa;
}

.activity-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 0.8rem;
    color: white;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-text {
    font-size: 0.9rem;
    line-height: 1.4;
}



/* Responsive */
@media (max-width: 768px) {
    .stats-card {
        padding: 1rem;
    }
    
    .stats-number {
        font-size: 2rem;
    }
    
    .project-card {
        padding: 1rem;
    }
    
    .project-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .project-status {
        margin-left: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
function verFases(projectId) {
    window.location.href = `/fabricasoft/desarrollador/proyecto/${projectId}`;
}



// Animaciones al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Animar las tarjetas de estadísticas
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });
    
    // Animar las tarjetas de proyectos
    const projectCards = document.querySelectorAll('.project-card');
    projectCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 100);
        }, index * 150);
    });
});
</script>
@endpush
