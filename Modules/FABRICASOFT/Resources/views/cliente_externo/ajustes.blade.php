@extends('fabricasoft::layouts.app')
@section('title', 'Ajustes del Perfil - FABRICASOFT')
@section('content')

<div class="container-fluid">
    <!-- Header de la Página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog me-2"></i>Ajustes del Perfil
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

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Mensaje Informativo -->
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Gestiona tu información personal</h6>
                        <p class="mb-0">Aquí puedes actualizar tu información personal, teléfono, empresa y cambiar tu contraseña. Los cambios se reflejarán en todos los formularios del sistema.</p>
                    </div>
                </div>
            </div>
            
            <!-- Formulario de Ajustes -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-edit me-2"></i>Información Personal
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('cliente_externo.ajustes.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nombre Completo *
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name', $datosPerfil['name']) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tu nombre completo como aparece en el sistema</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico
                                </label>
                                <input type="email" class="form-control" value="{{ $datosPerfil['email'] }}" readonly>
                                <small class="text-muted">El correo no se puede modificar por seguridad</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Teléfono
                                </label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       value="{{ old('phone', $datosPerfil['phone']) }}"
                                       placeholder="Ej: +57 300 123 4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Número de contacto para consultas sobre tus proyectos</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="organization" class="form-label">
                                    <i class="fas fa-building me-1"></i>Empresa/Institución
                                </label>
                                <input type="text" class="form-control @error('organization') is-invalid @enderror" 
                                       id="organization" name="organization" 
                                       value="{{ old('organization', $datosPerfil['organization']) }}"
                                       placeholder="Nombre de tu empresa o institución">
                                @error('organization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Organización para la cual trabajas</small>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Cambio de Contraseña -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h6 class="text-primary">
                                    <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                                </h6>
                                <small class="text-muted">Deja estos campos vacíos si no quieres cambiar tu contraseña</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-key me-1"></i>Contraseña Actual
                                </label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Nueva Contraseña
                                </label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" name="new_password" 
                                       minlength="8">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Mínimo 8 caracteres</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="new_password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle me-1"></i>Confirmar Nueva Contraseña
                                </label>
                                <input type="password" class="form-control" 
                                       id="new_password_confirmation" name="new_password_confirmation">
                                <small class="text-muted">Repite la nueva contraseña</small>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('cliente_externo.dashboard') }}" class="btn btn-sena-outline me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-sena">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Información Adicional -->
            <div class="card shadow mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-shield-alt me-2"></i>
                        Seguridad de tu cuenta
                    </h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-user-shield fa-2x text-primary mb-2"></i>
                                <h6>Datos Protegidos</h6>
                                <p class="text-muted small">Tu información personal está segura y solo se usa para gestionar tus proyectos</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-sync-alt fa-2x text-info mb-2"></i>
                                <h6>Sincronización</h6>
                                <p class="text-muted small">Los cambios se reflejan automáticamente en todos los formularios del sistema</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-history fa-2x text-success mb-2"></i>
                                <h6>Historial</h6>
                                <p class="text-muted small">Mantenemos un registro de todos los cambios realizados en tu perfil</p>
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
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    const currentPassword = document.getElementById('current_password');
    
    // Validar que si se ingresa nueva contraseña, también se ingrese la actual
    newPassword.addEventListener('input', function() {
        if (this.value.length > 0) {
            currentPassword.setAttribute('required', 'required');
            confirmPassword.setAttribute('required', 'required');
        } else {
            currentPassword.removeAttribute('required');
            confirmPassword.removeAttribute('required');
        }
    });
    
    // Validar que las contraseñas coincidan
    confirmPassword.addEventListener('input', function() {
        if (this.value !== newPassword.value) {
            this.setCustomValidity('Las contraseñas no coinciden');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Mostrar indicador de envío
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        submitBtn.disabled = true;
    });
    
    // Contador de caracteres para la nueva contraseña
    newPassword.addEventListener('input', function() {
        const remaining = 8 - this.value.length;
        const counter = this.parentNode.querySelector('.char-counter') || 
                       this.parentNode.appendChild(document.createElement('small'));
        
        counter.className = 'text-muted char-counter';
        
        if (remaining > 0) {
            counter.innerHTML = `Faltan ${remaining} caracteres`;
            counter.className = 'text-warning char-counter';
        } else {
            counter.innerHTML = 'Contraseña válida';
            counter.className = 'text-success char-counter';
        }
    });
});
</script>
@endpush
