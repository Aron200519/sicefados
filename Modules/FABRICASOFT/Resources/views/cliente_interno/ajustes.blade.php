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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong> Por favor corrige los siguientes errores:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                    <form action="{{ route('fabricasoft.cliente_interno.ajustes.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nombre Completo *
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name', $datosPerfil['name'] ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tu nombre completo como aparece en el sistema</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico
                                </label>
                                <input type="email" class="form-control" value="{{ $datosPerfil['email'] ?? '' }}" readonly>
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
                                       value="{{ old('phone', $datosPerfil['phone'] ?? '') }}"
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
                                       value="{{ old('organization', $datosPerfil['organization'] ?? '') }}"
                                       placeholder="Ej: SENA Regional">
                                @error('organization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Organización para la cual trabajas</small>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-sena">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Cambiar Contraseña -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('fabricasoft.cliente_interno.ajustes.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="update_password" value="1">
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Deja estos campos vacíos si no quieres cambiar tu contraseña</strong>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-key me-1"></i>Contraseña Actual
                                </label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Nueva Contraseña
                                </label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" name="new_password">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="new_password_confirmation" class="form-label">
                                    <i class="fas fa-check me-1"></i>Confirmar Nueva Contraseña
                                </label>
                                <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                       id="new_password_confirmation" name="new_password_confirmation">
                                @error('new_password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i>Cambiar Contraseña
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

.alert-danger {
    background-color: #ffebee;
    border-color: #ef5350;
    color: #c62828;
}
</style>
@endpush
