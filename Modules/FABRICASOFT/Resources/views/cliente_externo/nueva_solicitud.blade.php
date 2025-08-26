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
            <a href="{{ route('cliente_externo.dashboard') }}" class="btn btn-sena me-2">
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
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>Teléfono
                            </label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" 
                                   value="{{ old('phone', $persona->phone ?? $preregistroExistente->phone ?? '') }}"
                                   placeholder="Ej: +57 300 123 4567">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Número de contacto para consultas sobre tu proyecto</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="organization" class="form-label">
                                <i class="fas fa-building me-1"></i>Empresa/Institución
                            </label>
                            <input type="text" class="form-control @error('organization') is-invalid @enderror" 
                                   id="organization" name="organization" 
                                   value="{{ old('organization', $persona->organization ?? $preregistroExistente->organization ?? '') }}"
                                   placeholder="Nombre de tu empresa o institución">
                            @error('organization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Organización para la cual solicitas el servicio</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Solicitud de Software -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clipboard-list me-2"></i>Información del Proyecto
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cliente_externo.nueva-solicitud.store') }}" method="POST" 
                          @if(isset($proyectoActivo) && $proyectoActivo) disabled @endif>
                        @csrf
                        
                        <div class="mb-3">
                            <label for="software_type" class="form-label">
                                <i class="fas fa-laptop-code me-1"></i>Tipo de Software *
                            </label>
                            <select class="form-select @error('software_type') is-invalid @enderror" 
                                    id="software_type" name="software_type" required
                                    @if(isset($proyectoActivo) && $proyectoActivo) disabled @endif>
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
                            <small class="text-muted">Selecciona la categoría que mejor describe tu proyecto</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="project_description" class="form-label">
                                <i class="fas fa-file-alt me-1"></i>Descripción del Proyecto *
                            </label>
                            <textarea class="form-control @error('project_description') is-invalid @enderror" 
                                      id="project_description" name="project_description" rows="5" 
                                      placeholder="Describe detalladamente tu proyecto de software, incluyendo funcionalidades principales, objetivos, usuarios objetivo y cualquier requisito específico..." required
                                      @if(isset($proyectoActivo) && $proyectoActivo) disabled @endif>{{ old('project_description') }}</textarea>
                            @error('project_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Cuanto más detallada sea la descripción, mejor podremos entender tus necesidades</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional_requirements" class="form-label">
                                <i class="fas fa-list-check me-1"></i>Requisitos Adicionales
                            </label>
                            <textarea class="form-control @error('additional_requirements') is-invalid @enderror" 
                                      id="additional_requirements" name="additional_requirements" rows="3" 
                                      placeholder="Menciona requisitos técnicos específicos, integraciones necesarias, tecnologías preferidas o cualquier otra consideración importante..."
                                      @if(isset($proyectoActivo) && $proyectoActivo) disabled @endif>{{ old('additional_requirements') }}</textarea>
                            @error('additional_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Información adicional que nos ayude a planificar mejor tu proyecto</small>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input @error('terms_accepted') is-invalid @enderror" 
                                   type="checkbox" id="terms_accepted" name="terms_accepted" required
                                   @if(isset($proyectoActivo) && $proyectoActivo) disabled @endif>
                            <label class="form-check-label" for="terms_accepted">
                                Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a> del servicio de desarrollo de software FABRICASOFT *
                            </label>
                            @error('terms_accepted')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('cliente_externo.dashboard') }}" class="btn btn-sena-outline me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-sena" 
                                    @if(isset($proyectoActivo) && $proyectoActivo) disabled @endif>
                                <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Información Adicional -->
            <div class="card shadow mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>
                        ¿Qué sucede después de enviar tu solicitud?
                    </h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-search fa-2x text-primary mb-2"></i>
                                <h6>1. Revisión</h6>
                                <p class="text-muted small">Nuestro equipo revisará tu solicitud y se pondrá en contacto contigo</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-comments fa-2x text-info mb-2"></i>
                                <h6>2. Análisis</h6>
                                <p class="text-muted small">Realizaremos un análisis detallado de tus requerimientos</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-project-diagram fa-2x text-success mb-2"></i>
                                <h6>3. Proyecto</h6>
                                <p class="text-muted small">Una vez aprobado, crearemos tu equipo Scrum</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const form = document.querySelector('form');
    const termsCheckbox = document.getElementById('terms_accepted');
    
    form.addEventListener('submit', function(e) {
        if (!termsCheckbox.checked) {
            e.preventDefault();
            alert('Debes aceptar los términos y condiciones para continuar.');
            termsCheckbox.focus();
            return false;
        }
        
        // Mostrar indicador de envío
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
        submitBtn.disabled = true;
    });
    
    // Contador de caracteres para la descripción del proyecto
    const projectDescription = document.getElementById('project_description');
    const maxLength = 1000;
    
    projectDescription.addEventListener('input', function() {
        const remaining = maxLength - this.value.length;
        const counter = this.parentNode.querySelector('.char-counter') || 
                       this.parentNode.appendChild(document.createElement('small'));
        
        counter.className = 'text-muted char-counter';
        counter.innerHTML = `${remaining} caracteres restantes`;
        
        if (remaining < 100) {
            counter.className = 'text-warning char-counter';
        }
        if (remaining < 0) {
            counter.className = 'text-danger char-counter';
        }
    });
});
</script>
@endpush
