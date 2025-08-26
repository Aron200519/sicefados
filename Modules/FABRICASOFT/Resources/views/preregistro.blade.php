@extends('fabricasoft::layouts.app')
@section('title', 'Solicitud de Desarrollo de Software - FABRICASOFT')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Header del Formulario -->
        <div class="text-center mb-5">
            <div class="welcome-hero" style="padding: 40px 0;">
                <h2><i class="fas fa-code me-3"></i>Solicitud de Desarrollo de Software</h2>
                <p class="lead mb-0">Completa el formulario para solicitar el desarrollo de tu software personalizado</p>
            </div>
        </div>

        <!-- Formulario de Solicitud de Software -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Información del Proyecto
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('fabricasoft.preregistro.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">
                                <i class="fas fa-user me-1"></i>Nombre Completo *
                            </label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                   id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Correo Electrónico *
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>Teléfono
                            </label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="organization" class="form-label">
                                <i class="fas fa-building me-1"></i>Empresa/Institución *
                            </label>
                            <input type="text" class="form-control @error('organization') is-invalid @enderror" 
                                   id="organization" name="organization" value="{{ old('organization') }}" required>
                            @error('organization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Contraseña *
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" 
                                   placeholder="Mínimo 8 caracteres" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small class="text-muted">Esta contraseña te permitirá acceder al sistema después de que tu solicitud sea aprobada</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1"></i>Confirmar Contraseña *
                            </label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" 
                                   placeholder="Repite la contraseña" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="software_type" class="form-label">
                            <i class="fas fa-laptop-code me-1"></i>Tipo de Software *
                        </label>
                        <select class="form-select @error('software_type') is-invalid @enderror" 
                                id="software_type" name="software_type" required>
                            <option value="">Selecciona el tipo</option>
                            <option value="Sistema Web" {{ old('software_type') == 'Sistema Web' ? 'selected' : '' }}>
                                Sistema Web
                            </option>
                            <option value="Aplicación Móvil" {{ old('software_type') == 'Aplicación Móvil' ? 'selected' : '' }}>
                                Aplicación Móvil
                            </option>
                            <option value="Sistema de Escritorio" {{ old('software_type') == 'Sistema de Escritorio' ? 'selected' : '' }}>
                                Sistema de Escritorio
                            </option>
                            <option value="E-commerce" {{ old('software_type') == 'E-commerce' ? 'selected' : '' }}>
                                E-commerce
                            </option>
                            <option value="CRM" {{ old('software_type') == 'CRM' ? 'selected' : '' }}>
                                CRM
                            </option>
                            <option value="ERP" {{ old('software_type') == 'ERP' ? 'selected' : '' }}>
                                ERP
                            </option>
                            <option value="Otro" {{ old('software_type') == 'Otro' ? 'selected' : '' }}>
                                Otro
                            </option>
                        </select>
                        @error('software_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="project_description" class="form-label">
                            <i class="fas fa-file-alt me-1"></i>Descripción del Proyecto *
                        </label>
                        <textarea class="form-control @error('project_description') is-invalid @enderror" 
                                  id="project_description" name="project_description" rows="5" 
                                  placeholder="Describe detalladamente tu proyecto de software, incluyendo funcionalidades principales, objetivos, usuarios objetivo y cualquier requisito específico..." required>{{ old('project_description') }}</textarea>
                        @error('project_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="additional_requirements" class="form-label">
                            <i class="fas fa-list-check me-1"></i>Requisitos Adicionales
                        </label>
                        <textarea class="form-control @error('additional_requirements') is-invalid @enderror" 
                                  id="additional_requirements" name="additional_requirements" rows="3" 
                                  placeholder="Menciona requisitos técnicos específicos, integraciones necesarias, tecnologías preferidas o cualquier otra consideración importante...">{{ old('additional_requirements') }}</textarea>
                        @error('additional_requirements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input @error('terms_accepted') is-invalid @enderror" 
                               type="checkbox" id="terms_accepted" name="terms_accepted" required>
                        <label class="form-check-label" for="terms_accepted">
                            Acepto los <a href="#" class="text-decoration-none">términos y condiciones</a> del servicio de desarrollo de software FABRICASOFT *
                        </label>
                        @error('terms_accepted')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                        <label class="form-check-label" for="newsletter">
                            Deseo recibir notificaciones sobre nuevos servicios y actualizaciones de FABRICASOFT
                        </label>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('fabricasoft.index') }}" class="btn btn-sena-outline me-md-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-sena">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Información Adicional -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-info-circle me-2"></i>
                    ¿Qué sucede después de enviar tu solicitud?
                </h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <div class="feature-icon mb-2">
                                <i class="fas fa-search"></i>
                            </div>
                            <h6>Análisis Técnico</h6>
                            <p class="small text-muted">Nuestro equipo técnico analizará tu proyecto en 24-48 horas</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <div class="feature-icon mb-2">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h6>Contacto Directo</h6>
                            <p class="small text-muted">Te llamaremos para discutir detalles y crear una propuesta personalizada</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <div class="feature-icon mb-2">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <h6>Inicio del Proyecto</h6>
                            <p class="small text-muted">Una vez aprobado, comenzaremos el desarrollo de tu software</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
