@extends('fabricasoft::layouts.app')
@section('title', 'Dashboard Analista - FABRICASOFT')
@section('content')

<div class="container-fluid">
    <!-- Header del Dashboard -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line me-2"></i>Dashboard del Analista
        </h1>

    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Asignadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_assigned'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Pendientes de Análisis
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_analysis'] }}</div>
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
                                En Análisis
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['in_progress'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
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
                                Completadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Solicitudes Asignadas Recientes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-tasks me-2"></i>Solicitudes Asignadas Recientes
            </h6>
            <a href="{{ route('fabricasoft.analyst.solicitudes') }}" class="btn btn-sm btn-sena">
                Ver Todas
            </a>
        </div>
        <div class="card-body">
            @if($assignedRequests->count() > 0)
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
                            @foreach($assignedRequests as $request)
                            <tr>
                                <td>#{{ $request->id }}</td>
                                <td>
                                    <strong>{{ $request->full_name }}</strong><br>
                                    <small class="text-muted">{{ $request->email }}</small><br>
                                    <small class="text-muted">{{ $request->organization }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $request->software_type }}</span><br>
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
                    @if(method_exists($assignedRequests, 'links'))
                        {{ $assignedRequests->links() }}
                    @endif
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No tienes solicitudes asignadas</h5>
                    <p class="text-gray-400">Cuando el administrador te asigne solicitudes, aparecerán aquí.</p>
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
