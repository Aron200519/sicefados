@extends('fabricasoft::layouts.app')
@section('title', 'Iniciar Sesión - Cliente Externo - FABRICASOFT')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <!-- Header del Login -->
        <div class="text-center mb-5">
            <div class="welcome-hero" style="padding: 40px 0;">
                <h2><i class="fas fa-sign-in-alt me-3"></i>Iniciar Sesión</h2>
                <p class="lead mb-0">Accede a tu cuenta de cliente externo FABRICASOFT</p>
            </div>
        </div>

        <!-- Formulario de Login -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-lock me-2"></i>
                    Acceso de Cliente Externo
                </h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('fabricasoft.client.login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Correo Electrónico *
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" 
                               placeholder="correo@ejemplo.com" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Contraseña *
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" 
                               placeholder="Ingresa tu contraseña" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @error('error')
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                        </div>
                    @enderror
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-sena btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
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
                    ¿No tienes una cuenta?
                </h5>
                <p class="card-text">
                    Si aún no has enviado tu solicitud de desarrollo de software, 
                    <a href="{{ route('fabricasoft.preregistro') }}" class="text-decoration-none">haz clic aquí</a> 
                    para registrarte.
                </p>
                <p class="card-text">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Solo podrás acceder después de que tu solicitud sea aprobada por nuestro equipo.
                    </small>
                </p>
            </div>
        </div>
        
        <!-- Enlaces de Navegación -->
        <div class="text-center mt-4">
            <a href="{{ route('fabricasoft.index') }}" class="btn btn-sena-outline">
                <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
            </a>
        </div>
    </div>
</div>

@endsection
