@extends('fabricasoft::layouts.app')
@section('title', 'Nueva Solicitud de Servicio - FABRICASOFT')
@section('content')

<div class="container-fluid">
    <!-- Header de la Página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle me-2"></i>Nueva Solicitud de Servicio
        </h1>
        <div>
            <a href="{{ route('fabricasoft.cliente_interno.dashboard') }}" class="btn btn-sena me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Alertas de Mensajes -->
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

    <!-- Mensaje de Proyecto Activo -->
    @if(isset($proyectoActivo) && $proyectoActivo)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">⚠️ No puedes crear una nueva solicitud</h6>
                    <p class="mb-0">
                        Ya tienes un proyecto activo: <strong>"{{ $proyectoActivo->project_name }}"</strong> 
                        (Estado: {{ $proyectoActivo->status_text }}). 
                        <br>Debes completar este proyecto antes de solicitar uno nuevo.
                    </p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Mensaje Informativo -->
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">¡Bienvenido de vuelta!</h6>
                        <p class="mb-0">Como cliente registrado, tu información personal se obtiene automáticamente. Solo necesitas completar los detalles de tu nuevo proyecto.</p>
                    </div>
                </div>
            </div>
            
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
                            <label class="form-label">
                                <i class="fas fa-user me-1"></i>Nombre Completo
                                <span class="badge bg-success ms-2">Automático</span>
                            </label>
                            <input type="text" class="form-control" 
                                   value="{{ $user->nickname ?: 'Nombre no configurado' }}" 
                                   readonly>
                            <small class="text-muted">
                                @if($user->nickname)
                                    Esta información se obtiene automáticamente de tu cuenta
                                @else
                                    <span class="text-warning">⚠️ Tu nombre no está configurado en el sistema</span>
                                @endif
                            </small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope me-1"></i>Correo Electrónico
                                <span class="badge bg-success ms-2">Automático</span>
                            </label>
                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                            <small class="text-muted">Este es tu correo de contacto principal</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-phone me-1"></i>Teléfono
                                <span class="badge bg-success ms-2">Automático</span>
                            </label>
                            <input type="tel" class="form-control" 
                                   value="{{ $user->phone ?: 'Teléfono no configurado' }}" 
                                   readonly>
                            <small class="text-muted">
                                @if($user->phone)
                                    Tu número de contacto para consultas sobre el proyecto
                                @else
                                    <span class="text-warning">⚠️ Tu teléfono no está configurado en el sistema</span>
                                @endif
                            </small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-building me-1"></i>Empresa/Institución
                                <span class="badge bg-success ms-2">Automático</span>
                            </label>
                            <input type="text" class="form-control" 
                                   value="{{ $preregistroExistente->organization ?: 'Organización no configurada' }}" 
                                   readonly>
                            <small class="text-muted">
                                @if($preregistroExistente->organization)
                                    Tu organización para la cual trabajas
                                @else
                                    <span class="text-warning">⚠️ Tu organización no está configurada en el sistema</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Formulario de Solicitud -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-project-diagram me-2"></i>Detalles del Proyecto
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('fabricasoft.cliente_interno.nueva-solicitud.store') }}" method="POST">
                        @csrf
                        
                        <!-- Campos ocultos para datos automáticos -->
                        <input type="hidden" name="phone" value="{{ $user->phone ?? '' }}">
                        <input type="hidden" name="organization" value="{{ $preregistroExistente->organization ?? 'SENA' }}">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="software_type" class="form-label">
                                    <i class="fas fa-code me-1"></i>Tipo de Software *
                                </label>
                                <select class="form-select @error('software_type') is-invalid @enderror" 
                                        id="software_type" name="software_type" required>
                                    <option value="">Selecciona el tipo de software</option>
                                    <option value="aplicacion_web" {{ old('software_type') == 'aplicacion_web' ? 'selected' : '' }}>
                                        Aplicación Web
                                    </option>
                                    <option value="aplicacion_movil" {{ old('software_type') == 'aplicacion_movil' ? 'selected' : '' }}>
                                        Aplicación Móvil
                                    </option>
                                    <option value="sistema_desktop" {{ old('software_type') == 'sistema_desktop' ? 'selected' : '' }}>
                                        Sistema Desktop
                                    </option>
                                    <option value="base_datos" {{ old('software_type') == 'base_datos' ? 'selected' : '' }}>
                                        Base de Datos
                                    </option>
                                    <option value="api" {{ old('software_type') == 'api' ? 'selected' : '' }}>
                                        API
                                    </option>
                                    <option value="otro" {{ old('software_type') == 'otro' ? 'selected' : '' }}>
                                        Otro
                                    </option>
                                </select>
                                @error('software_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Selecciona el tipo de software que necesitas desarrollar</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="client_type" class="form-label">
                                    <i class="fas fa-users me-1"></i>Tipo de Cliente *
                                </label>
                                <select class="form-select @error('client_type') is-invalid @enderror" 
                                        id="client_type" name="client_type" required>
                                    <option value="">Selecciona el tipo de cliente</option>
                                    <option value="cliente_interno" {{ old('client_type') == 'cliente_interno' ? 'selected' : '' }}>
                                        Cliente Interno (SENA)
                                    </option>
                                </select>
                                @error('client_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tipo de cliente para el proyecto</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="project_description" class="form-label">
                                <i class="fas fa-file-alt me-1"></i>Descripción del Proyecto *
                            </label>
                            <textarea class="form-control @error('project_description') is-invalid @enderror" 
                                      id="project_description" name="project_description" 
                                      rows="4" placeholder="Describe detalladamente tu proyecto, objetivos, funcionalidades principales..." required>{{ old('project_description') }}</textarea>
                            @error('project_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Describe tu proyecto de manera detallada para que el equipo pueda entender mejor tus necesidades</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional_requirements" class="form-label">
                                <i class="fas fa-list-check me-1"></i>Requisitos Adicionales
                            </label>
                            <textarea class="form-control @error('additional_requirements') is-invalid @enderror" 
                                      id="additional_requirements" name="additional_requirements" 
                                      rows="3" placeholder="Requisitos técnicos, preferencias de tecnología, restricciones...">{{ old('additional_requirements') }}</textarea>
                            @error('additional_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Requisitos técnicos, preferencias de tecnología o cualquier restricción que debamos considerar</small>
                        </div>
                        
                        <!-- Términos y Condiciones -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('terms_accepted') is-invalid @enderror" 
                                       type="checkbox" id="terms_accepted" name="terms_accepted" value="1" 
                                       {{ old('terms_accepted') ? 'checked' : '' }} required>
                                <label class="form-check-label" for="terms_accepted">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a> del servicio *
                                </label>
                                @error('terms_accepted')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-sena btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 10px 10px 0 0 !important;
}

.form-control:focus {
    border-color: var(--sena-green);
    box-shadow: 0 0 0 0.2rem rgba(57, 169, 0, 0.25);
}

.form-select:focus {
    border-color: var(--sena-green);
    box-shadow: 0 0 0 0.2rem rgba(57, 169, 0, 0.25);
}

.btn-sena {
    background-color: var(--sena-green);
    border-color: var(--sena-green);
    color: white;
}

.btn-sena:hover {
    background-color: var(--sena-dark-green);
    border-color: var(--sena-dark-green);
    color: white;
}

.alert-info {
    background-color: #e3f2fd;
    border-color: #90caf9;
    color: #1565c0;
}

.alert-warning {
    background-color: #fff3e0;
    border-color: #ffcc02;
    color: #e65100;
}
</style>
@endpush
