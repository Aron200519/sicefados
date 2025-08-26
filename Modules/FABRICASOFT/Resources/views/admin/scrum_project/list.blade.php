@extends('fabricasoft::layouts.app')
@section('title', 'Equipos Scrum - FABRICASOFT')
@section('content')

<div class="container-fluid">
    <!-- Header del Dashboard -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram me-2"></i>Equipos Scrum
        </h1>
        <div>
            <a href="{{ route('fabricasoft.admin.dashboard') }}" class="btn btn-sena me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Proyectos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
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
                                En Planificación
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->where('status', 'planning')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
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
                                Completados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->where('status', 'completed')->count() }}</div>
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
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Lista de Equipos Scrum
            </h6>
        </div>
        <div class="card-body">
            @if($projects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Proyecto</th>
                                <th>Cliente</th>
                                <th>Scrum Master</th>
                                <th>Estado</th>
                                <th>Miembros</th>
                                <th>Fechas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <tr>
                                <td>#{{ $project->id }}</td>
                                <td>
                                    <strong>{{ $project->project_name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($project->description, 80) }}</small>
                                </td>
                                <td>
                                    @if($project->preregistration)
                                        <strong>{{ $project->preregistration->full_name }}</strong><br>
                                        <small class="text-muted">{{ $project->preregistration->organization ?: 'Organización no disponible' }}</small>
                                    @else
                                        <span class="text-muted">Cliente no disponible</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $project->scrumMaster->nickname ?? $project->scrumMaster->name }}</strong><br>
                                    <small class="text-muted">{{ $project->scrumMaster->email }}</small>
                                </td>
                                <td>
                                    @if($project->status == 'planning')
                                        <span class="badge bg-warning">{{ $project->status_text }}</span>
                                    @elseif($project->status == 'active')
                                        <span class="badge bg-info">{{ $project->status_text }}</span>
                                    @elseif($project->status == 'completed')
                                        <span class="badge bg-success">{{ $project->status_text }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $project->status_text }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $project->teamMembers->where('status', 'active')->count() }} miembros</span><br>
                                    <small class="text-muted">
                                        {{ $project->developers->count() }} dev, 
                                        {{ $project->testers->count() }} test, 
                                        {{ $project->designers->count() }} design
                                    </small>
                                    @if($project->teamMembers->where('status', 'active')->count() == 0)
                                        <br><span class="badge bg-warning">Sin equipo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($project->start_date)
                                        <small><strong>Inicio:</strong> {{ $project->start_date->format('d/m/Y') }}</small><br>
                                    @endif
                                    @if($project->estimated_end_date)
                                        <small><strong>Fin Est.:</strong> {{ $project->estimated_end_date->format('d/m/Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('fabricasoft.admin.projects.show', $project->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $projects->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-project-diagram fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No hay equipos Scrum creados</h5>
                    <p class="text-gray-400">Los proyectos se crean automáticamente cuando se aprueban solicitudes.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
