@extends('fabricasoft::layouts.app')
@section('title', 'Equipos Scrum - Desarrollador - FABRICASOFT')

@section('content')

<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram me-2"></i>Mis Equipos Scrum
        </h1>
        <div>
            <a href="{{ route('fabricasoft.desarrollador.dashboard') }}" class="btn btn-sena-outline">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Proyectos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_projects'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                En Desarrollo
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['in_development'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-code fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Completados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['completed'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Fases Completadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['completed_phases'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Proyectos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Proyectos Asignados
            </h6>
        </div>
        <div class="card-body">
            @if($projects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Proyecto</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Fases</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <tr>
                                <td>#{{ $project->id }}</td>
                                <td>
                                    <strong>{{ $project->project_name }}</strong><br>
                                    <small class="text-muted">{{ $project->description }}</small>
                                </td>
                                <td>
                                    @if($project->preregistration && $project->preregistration->full_name)
                                        <strong>{{ $project->preregistration->full_name }}</strong><br>
                                        <small class="text-muted">{{ $project->preregistration->email ?: 'Email no disponible' }}</small>
                                    @else
                                        <span class="text-muted">Cliente no disponible</span>
                                    @endif
                                </td>
                                <td>
                                    @if($project->status == 'pending')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif($project->status == 'active')
                                        <span class="badge bg-success">En Desarrollo</span>
                                    @elseif($project->status == 'completed')
                                        <span class="badge bg-info">Completado</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($project->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $completedPhases = $project->phases->where('status', 'completed')->count();
                                        $totalPhases = $project->phases->count();
                                    @endphp
                                    <span class="badge bg-primary">{{ $completedPhases }}/{{ $totalPhases }}</span>
                                    @if($completedPhases > 0)
                                        <br><small class="text-muted">{{ $completedPhases }} de {{ $totalPhases }} fases completadas</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $project->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('fabricasoft.desarrollador.projects.show', $project->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Ver Proyecto">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($project->status == 'in_progress')
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="verFasesActivas({{ $project->id }})" title="Ver Fases">
                                                <i class="fas fa-tasks"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-project-diagram fa-4x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-500">No tienes proyectos asignados</h4>
                    <p class="text-gray-400">Los equipos Scrum se asignan automáticamente cuando se aprueban las solicitudes.</p>
                    <a href="{{ route('fabricasoft.desarrollador.dashboard') }}" class="btn btn-sena">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function verFasesActivas(projectId) {
    // Redirigir a la vista del proyecto para ver las fases
    window.location.href = "{{ route('fabricasoft.desarrollador.projects.show', ':id') }}".replace(':id', projectId);
}
</script>
@endpush
