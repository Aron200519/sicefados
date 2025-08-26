@extends('fabricasoft::layouts.app')
@section('title', 'Mis Proyectos - FABRICASOFT')
@section('content')



<div class="container-fluid">
    <!-- Header de la Página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram me-2"></i>Mis Proyectos
        </h1>
        <div>
            <a href="{{ route('fabricasoft.cliente_interno.dashboard') }}" class="btn btn-sena">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
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

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                TOTAL PROYECTOS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $proyectos->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                EN PLANIFICACIÓN
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $proyectos->where('status', 'planning')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                ACTIVOS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $proyectos->where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                COMPLETADOS
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $proyectos->where('status', 'completed')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filtros y Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchProjects" placeholder="Buscar proyectos...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">Todos los estados</option>
                        <option value="planning">En Planificación</option>
                        <option value="active">Activo</option>
                        <option value="completed">Completado</option>
                        <option value="paused">Pausado</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterType">
                        <option value="">Todos los tipos</option>
                        <option value="aplicacion_web">Aplicación Web</option>
                        <option value="aplicacion_movil">Aplicación Móvil</option>
                        <option value="sistema_desktop">Sistema Desktop</option>
                        <option value="base_datos">Base de Datos</option>
                        <option value="api">API</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Equipos Scrum -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Lista de Equipos Scrum
            </h6>
        </div>
        <div class="card-body">
            @if(!$preregistration)
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                    <h4 class="text-warning">No tienes solicitudes registradas</h4>
                    <p class="text-muted">Para ver proyectos, primero debes crear una solicitud de desarrollo de software.</p>
                    <a href="{{ route('fabricasoft.cliente_interno.nueva-solicitud') }}" class="btn btn-sena">
                        <i class="fas fa-plus me-2"></i>Crear Nueva Solicitud
                    </a>
                </div>
            @elseif($proyectos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Proyecto</th>
                                <th>Cliente</th>
                                <th>Scrum Master</th>
                                <th>Estado</th>
                                <th>Progreso</th>
                                <th>Miembros</th>
                                <th>Fechas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proyectos as $proyecto)
                                @php
                                    // Calcular progreso del proyecto
                                    $totalPhases = $proyecto->phases->count();
                                    $completedPhases = $proyecto->phases->where('status', 'completed')->count();
                                    $progressPercentage = $totalPhases > 0 ? round(($completedPhases / $totalPhases) * 100) : 0;
                                @endphp
                                
                                <tr class="project-item" 
                                    data-status="{{ $proyecto->status }}" 
                                    data-type="{{ $proyecto->preregistration->software_type ?? '' }}">
                                    <td>
                                        <span class="badge bg-secondary">#{{ $proyecto->id }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ Str::limit($proyecto->preregistration->project_description ?: 'Proyecto #' . $proyecto->id, 50) }}</strong>
                                        @if($proyecto->preregistration->project_description)
                                            <br><small class="text-muted">{{ Str::limit($proyecto->preregistration->project_description, 100) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $proyecto->client_name }}</strong>
                                            @if($proyecto->preregistration->organization)
                                                <small class="text-muted">{{ $proyecto->preregistration->organization }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $proyecto->scrumMaster->nickname ?? $proyecto->scrumMaster->name ?? 'No asignado' }}</strong>
                                            @if($proyecto->scrumMaster)
                                                <small class="text-muted">{{ $proyecto->scrumMaster->email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = 'bg-secondary';
                                            $statusText = 'Pendiente';
                                            
                                            switch($proyecto->status) {
                                                case 'planning':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'En Planificación';
                                                    break;
                                                case 'active':
                                                    $statusClass = 'bg-info';
                                                    $statusText = 'Activo';
                                                    break;
                                                case 'completed':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Completado';
                                                    break;
                                                case 'paused':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Pausado';
                                                    break;
                                                case 'cancelled':
                                                    $statusClass = 'bg-danger';
                                                    $statusText = 'Cancelado';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        @if($totalPhases > 0)
                                            <div class="d-flex flex-column align-items-center">
                                                <div class="progress mb-1" style="width: 80px; height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ $progressPercentage }}%" 
                                                         aria-valuenow="{{ $progressPercentage }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $progressPercentage }}%</small>
                                                <small class="text-muted">{{ $completedPhases }}/{{ $totalPhases }} fases</small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Sin fases</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $totalMembers = $proyecto->teamMembers->where('status', 'active')->count() + 1; // +1 por el Scrum Master
                                            $developers = $proyecto->developers->count();
                                            $testers = $proyecto->teamMembers->where('role', 'tester')->count();
                                            $designers = $proyecto->teamMembers->where('role', 'designer')->count();
                                        @endphp
                                        
                                        @if($totalMembers > 1)
                                            <div class="d-flex flex-column">
                                                <span class="badge bg-primary">{{ $totalMembers }} miembros</span>
                                                <small class="text-muted">{{ $developers }} dev, {{ $testers }} test, {{ $designers }} design</small>
                                            </div>
                                        @else
                                            <span class="badge bg-warning">Sin equipo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($proyecto->start_date)
                                                <small class="text-muted">Inicio: {{ $proyecto->start_date->format('d/m/Y') }}</small>
                                            @endif
                                            @if($proyecto->estimated_end_date)
                                                <small class="text-muted">Fin Est.: {{ $proyecto->estimated_end_date->format('d/m/Y') }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('fabricasoft.cliente_interno.projects.show', $proyecto->id) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye me-1"></i>Ver Avance
                                            </a>
                                            @if($proyecto->status === 'completed')
                                                <span class="badge bg-success">✓ Completado</span>
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
                    {{ $proyectos->links() }}
                </div>
                
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No tienes proyectos contratados</h4>
                    <p class="text-muted">Una vez que se aprueben tus solicitudes y se creen los proyectos, aparecerán aquí.</p>
                    <a href="{{ route('fabricasoft.cliente_interno.dashboard') }}" class="btn btn-sena">
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchProjects');
    const statusFilter = document.getElementById('filterStatus');
    const typeFilter = document.getElementById('filterType');
    
    function filterProjects() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;
        const selectedType = typeFilter.value;
        
        const projectItems = document.querySelectorAll('.project-item');
        
        projectItems.forEach(item => {
            const projectText = item.textContent.toLowerCase();
            const status = item.dataset.status;
            const type = item.dataset.type;
            
            const matchesSearch = projectText.includes(searchTerm);
            const matchesStatus = !selectedStatus || status === selectedStatus;
            const matchesType = !selectedType || type === selectedType;
            
            if (matchesSearch && matchesStatus && matchesType) {
                item.style.display = 'table-row';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterProjects);
    statusFilter.addEventListener('change', filterProjects);
    typeFilter.addEventListener('change', filterProjects);
});
</script>
@endpush
