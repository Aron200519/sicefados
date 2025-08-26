@extends('fabricasoft::layouts.app')
@section('title', 'Solicitudes Asignadas - Analista FABRICASOFT')

@section('styles')
<style>
    .filter-section {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .filter-section .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        color: white;
    }
    
    .filter-section .form-label {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .filter-section .form-select,
    .filter-section .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .filter-section .form-select:focus,
    .filter-section .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .btn-filter {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 8px;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    }
    
    .btn-clear {
        background: #6c757d;
        border: none;
        border-radius: 8px;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-clear:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')

<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-list me-2"></i>Solicitudes Asignadas
        </h1>
        <div>
            <a href="{{ route('fabricasoft.analyst.dashboard') }}" class="btn btn-sena-outline">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4 filter-section">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-filter me-2"></i>Filtros
            </h6>
            <small class="text-white-50">Los filtros están estandarizados para coincidir con los tipos de software del sistema</small>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('fabricasoft.analyst.solicitudes') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado del Análisis</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos los estados</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="software_type" class="form-label">Tipo de Software</label>
                    <select class="form-select" id="software_type" name="software_type">
                        <option value="">Todos los tipos</option>
                        <option value="Sistema Web" {{ request('software_type') == 'Sistema Web' ? 'selected' : '' }}>Sistema Web</option>
                        <option value="Aplicación Móvil" {{ request('software_type') == 'Aplicación Móvil' ? 'selected' : '' }}>Aplicación Móvil</option>
                        <option value="Sistema de Escritorio" {{ request('software_type') == 'Sistema de Escritorio' ? 'selected' : '' }}>Sistema de Escritorio</option>
                        <option value="API" {{ request('software_type') == 'API' ? 'selected' : '' }}>API</option>
                        <option value="Otro" {{ request('software_type') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    <small class="form-text text-muted">Tipos de software disponibles en el sistema</small>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Fecha Inicial</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                

                
                <div class="col-12">
                    <button type="submit" class="btn btn-filter me-2">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('fabricasoft.analyst.solicitudes') }}" class="btn btn-clear">
                        <i class="fas fa-times me-2"></i>Limpiar Filtros
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Solicitudes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-tasks me-2"></i>Solicitudes Asignadas 
                @if(request()->hasAny(['status', 'software_type', 'date_from']))
                    <span class="text-muted">(Filtradas: {{ $requests->total() }})</span>
                @else
                    <span>({{ $requests->total() }})</span>
                @endif
            </h6>
            <div class="text-muted">
                Mostrando {{ $requests->firstItem() ?? 0 }} - {{ $requests->lastItem() ?? 0 }} de {{ $requests->total() }}
                @if(request()->hasAny(['status', 'software_type', 'date_from']))
                    <span class="text-info">(Filtrado)</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Proyecto</th>
                                <th>Estado Análisis</th>
                                <th>Asignada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>
                                    <strong>{{ $request->full_name }}</strong><br>
                                    <small class="text-muted">{{ $request->email }}</small><br>
                                    <small class="text-muted">{{ $request->organization }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        @switch($request->software_type)
                                            @case('Sistema Web')
                                                Sistema Web
                                                @break
                                            @case('Aplicación Móvil')
                                                Aplicación Móvil
                                                @break
                                            @case('Sistema de Escritorio')
                                                Sistema de Escritorio
                                                @break
                                            @case('API')
                                                API
                                                @break
                                            @default
                                                {{ $request->software_type }}
                                        @endswitch
                                    </span><br>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($request->project_description, 80) }}</small>
                                </td>
                                <td>
                                    @if($request->analysis_status == 'pending')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif($request->analysis_status == 'in_progress')
                                        <span class="badge bg-info">En Progreso</span>
                                    @else
                                        <span class="badge bg-success">Completado</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $request->assigned_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('fabricasoft.analyst.show.request', $request->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        
                                        @if($request->analysis_status == 'pending')
                                            <form action="{{ route('fabricasoft.analyst.iniciar.analisis', $request->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-play"></i> Iniciar
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($request->analysis_status == 'in_progress')
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="mostrarFormularioSRS({{ $request->id }})">
                                                <i class="fas fa-upload"></i> SRS
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $requests->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No se encontraron solicitudes</h5>
                    <p class="text-gray-400">
                                            @if(request()->hasAny(['status', 'software_type', 'date_from']))
                        No hay solicitudes que coincidan con los filtros aplicados.
                        <br><small class="text-muted">Intenta ajustar los criterios de búsqueda.</small>
                    @else
                        No tienes solicitudes asignadas actualmente.
                    @endif
                </p>
                @if(request()->hasAny(['status', 'software_type', 'date_from']))
                    <a href="{{ route('fabricasoft.analyst.solicitudes') }}" class="btn btn-outline-primary mt-2">
                        <i class="fas fa-refresh me-2"></i>Ver Todas las Solicitudes
                    </a>
                @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Subir SRS -->
<div class="modal fade" id="modalSRS" tabindex="-1" aria-labelledby="modalSRSLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSRSLabel">Subir SRS y Completar Análisis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSRS" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Completa el análisis subiendo el documento SRS y los requisitos detallados.
                    </div>
                    
                    <div class="mb-3">
                        <label for="srs_file" class="form-label">Archivo SRS *</label>
                        <input type="file" class="form-control" id="srs_file" name="srs_file" 
                               accept=".pdf,.doc,.docx" required>
                        <div class="form-text">Formatos permitidos: PDF, DOC, DOCX. Máximo 10MB.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="srs_requirements" class="form-label">Requisitos Detallados *</label>
                        <textarea class="form-control" id="srs_requirements" name="srs_requirements" 
                                  rows="6" placeholder="Describe detalladamente todos los requisitos del software..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="analyst_notes" class="form-label">Notas del Analista</label>
                        <textarea class="form-control" id="analyst_notes" name="analyst_notes" 
                                  rows="4" placeholder="Agrega notas adicionales, observaciones o recomendaciones..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-2"></i>Subir SRS y Completar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function mostrarFormularioSRS(requestId) {
    const form = document.getElementById('formSRS');
    form.action = `/fabricasoft/analyst/solicitud/${requestId}/subir-srs`;
    
    const modal = new bootstrap.Modal(document.getElementById('modalSRS'));
    modal.show();
}
</script>
@endpush
