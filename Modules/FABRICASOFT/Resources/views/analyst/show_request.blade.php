@extends('fabricasoft::layouts.app')
@section('title', 'Detalles de Solicitud #' . $request->id . ' - Analista FABRICASOFT')
@section('content')

<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye me-2"></i>Detalles de Solicitud #{{ $request->id }}
        </h1>
        <div>
            <a href="{{ route('fabricasoft.analyst.dashboard') }}" class="btn btn-sena-outline me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
            @if($request->analysis_status == 'pending')
                <form action="{{ route('fabricasoft.analyst.iniciar.analisis', $request->id) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-play me-2"></i>Iniciar Análisis
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Información del Cliente -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Información del Cliente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <p class="form-control-plaintext">{{ $request->full_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <p class="form-control-plaintext">
                                <a href="mailto:{{ $request->email }}" class="text-decoration-none">
                                    {{ $request->email }}
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Teléfono</label>
                            <p class="form-control-plaintext">
                                @if($request->phone)
                                    <a href="tel:{{ $request->phone }}" class="text-decoration-none">
                                        {{ $request->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Empresa/Institución</label>
                            <p class="form-control-plaintext">{{ $request->organization }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Proyecto -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-project-diagram me-2"></i>Detalles del Proyecto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Software</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info fs-6">{{ $request->software_type }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estado del Análisis</label>
                            <p class="form-control-plaintext">
                                @if($request->analysis_status == 'pending')
                                    <span class="badge bg-warning fs-6">Pendiente</span>
                                @elseif($request->analysis_status == 'in_progress')
                                    <span class="badge bg-info fs-6">En Progreso</span>
                                @else
                                    <span class="badge bg-success fs-6">Completado</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción del Proyecto</label>
                        <div class="form-control-plaintext" style="min-height: 100px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                            {{ $request->project_description }}
                        </div>
                    </div>
                    
                    @if($request->additional_requirements)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Requisitos Adicionales</label>
                        <div class="form-control-plaintext" style="min-height: 80px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                            {{ $request->additional_requirements }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Análisis y SRS -->
            @if($request->analysis_status == 'in_progress')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-upload me-2"></i>Subir Documentación SRS
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instrucciones:</strong> Sube el documento SRS y completa los requisitos detallados para finalizar el análisis.
                    </div>
                    
                    <form action="{{ route('fabricasoft.analyst.subir.srs', $request->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="srs_file" class="form-label">Archivo SRS *</label>
                            <input type="file" class="form-control" id="srs_file" name="srs_file" 
                                   accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Formatos permitidos: PDF, DOC, DOCX. Máximo 10MB.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="srs_requirements" class="form-label">Requisitos Detallados *</label>
                            <textarea class="form-control" id="srs_requirements" name="srs_requirements" 
                                      rows="6" placeholder="Describe detalladamente todos los requisitos del software, funcionalidades, especificaciones técnicas, etc..." required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="analyst_notes" class="form-label">Notas del Analista</label>
                            <textarea class="form-control" id="analyst_notes" name="analyst_notes" 
                                      rows="4" placeholder="Agrega notas adicionales, observaciones, recomendaciones o cualquier información relevante..."></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload me-2"></i>Subir SRS y Completar Análisis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- SRS Completado -->
            @if($request->analysis_status == 'completed' && $request->srs_file_path)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-check-circle me-2"></i>SRS Completado
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>¡Análisis Completado!</strong> El SRS ha sido subido exitosamente y está listo para revisión del administrador.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Requisitos Detallados</label>
                        <div class="form-control-plaintext" style="min-height: 80px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                            {{ $request->srs_requirements }}
                        </div>
                    </div>
                    
                    @if($request->analyst_notes)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notas del Analista</label>
                        <div class="form-control-plaintext" style="min-height: 80px; background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
                            {{ $request->analyst_notes }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Archivo SRS</label>
                        <div class="d-grid">
                            <a href="{{ route('fabricasoft.analyst.descargar.srs', $request->id) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-download me-2"></i>Descargar SRS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar con Información Adicional -->
        <div class="col-lg-4">
            <!-- Estado del Análisis -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Estado del Análisis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado Actual</label>
                        <div class="d-grid">
                            @if($request->analysis_status == 'pending')
                                <span class="badge bg-warning fs-5 py-2">Pendiente de Análisis</span>
                            @elseif($request->analysis_status == 'in_progress')
                                <span class="badge bg-info fs-5 py-2">En Análisis</span>
                            @else
                                <span class="badge bg-success fs-5 py-2">Completado</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Flujo de Trabajo</label>
                        <div class="d-grid">
                            <span class="badge bg-secondary fs-6 py-2">{{ $request->workflow_status_text }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-address-book me-2"></i>Contacto Rápido
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $request->email }}" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Enviar Email
                        </a>
                        @if($request->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $request->phone) }}" target="_blank" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                        </a>
                        @endif
                        <button type="button" class="btn btn-outline-info" onclick="copiarInformacion()">
                            <i class="fas fa-copy me-2"></i>Copiar Información
                        </button>
                    </div>
                </div>
            </div>

            <!-- Información Técnica -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información Técnica
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">ID de Solicitud:</small><br>
                        <strong>{{ $request->id }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Tipo de Cliente:</small><br>
                        <span class="badge bg-secondary">{{ $request->client_type == 'cliente_externo' ? 'Cliente Externo' : 'Cliente Interno' }}</span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Fecha de Creación:</small><br>
                        <strong>{{ $request->created_at->format('d/m/Y H:i:s') }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Fecha de Asignación:</small><br>
                        <strong>{{ $request->assigned_at->format('d/m/Y H:i:s') }}</strong>
                    </div>
                    @if($request->srs_uploaded_at)
                    <div class="mb-2">
                        <small class="text-muted">SRS Subido:</small><br>
                        <strong>{{ $request->srs_uploaded_at->format('d/m/Y H:i:s') }}</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function copiarInformacion() {
    const info = `Cliente: {{ $request->full_name }}
Email: {{ $request->email }}
Teléfono: {{ $request->phone ?? 'No especificado' }}
Empresa: {{ $request->organization }}
Proyecto: {{ $request->software_type }}
ID Solicitud: {{ $request->id }}`;
    
    navigator.clipboard.writeText(info).then(() => {
        // Mostrar notificación de éxito
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Copiado!';
        btn.classList.remove('btn-outline-info');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-info');
        }, 2000);
    });
}
</script>
@endpush
