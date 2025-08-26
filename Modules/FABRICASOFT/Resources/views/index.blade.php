@extends('fabricasoft::layouts.app')
@section('title', 'FABRICASOFT - Bienvenido al Sistema de Gestión de Proyectos')
@section('content')

@auth
    <!-- Mensaje para usuarios autenticados -->
    @if(session('no_role_message'))
        <div class="alert alert-warning text-center" role="alert">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                <h5 class="mb-0">Acceso Limitado</h5>
            </div>
            <p class="mb-3">{{ session('no_role_message') }}</p>
            <hr>
            <p class="small mb-0">Para obtener acceso completo, contacta al administrador del sistema.</p>
        </div>
    @else
        <div class="alert alert-info text-center" role="alert">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-user-check fa-2x text-info me-3"></i>
                <h5 class="mb-0">¡Bienvenido de vuelta!</h5>
            </div>
            <p class="mb-3">Ya estás autenticado. Serás redirigido automáticamente a tu dashboard.</p>
            <div class="mt-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 small text-muted">Redirigiendo...</p>
            </div>
        </div>
        
        <!-- Script para redirección automática -->
        <script>
            setTimeout(function() {
                @if(Auth::user()->hasCustomRole('fabricasoft.admin'))
                    window.location.href = '{{ route("fabricasoft.admin.dashboard") }}';
                @elseif(Auth::user()->hasCustomRole('fabricasoft.desarrollador'))
                    window.location.href = '{{ route("fabricasoft.desarrollador.dashboard") }}';
                @elseif(Auth::user()->hasCustomRole('fabricasoft.cliente_interno'))
                    window.location.href = '{{ route("fabricasoft.cliente_interno.dashboard") }}';
                @elseif(Auth::user()->hasCustomRole('fabricasoft.cliente_externo'))
                    window.location.href = '{{ route("cliente_externo.dashboard") }}';
                @endif
            }, 2000);
        </script>
    @endif
@else
    <!-- Hero Section -->
    <div class="welcome-hero">
        <div class="container">
            <h1><i class="fas fa-code me-3"></i>FABRICASOFT</h1>
            <p class="lead">Sistema de Gestión de Proyectos de Software del SENA</p>
            <p class="mb-0">Plataforma integral para la gestión eficiente de proyectos tecnológicos</p>
        </div>
    </div>

    <!-- Características Principales -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4>Colaboración en Equipo</h4>
                <p>Trabajo coordinado entre administradores, desarrolladores y clientes para optimizar el desarrollo de software.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4>Seguimiento en Tiempo Real</h4>
                <p>Monitoreo continuo del progreso de proyectos con métricas y reportes actualizados.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>Seguridad y Control</h4>
                <p>Acceso controlado por roles con permisos específicos para cada tipo de usuario.</p>
            </div>
        </div>
    </div>

    <!-- Opciones de Acceso -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</h4>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-sign-in-alt fa-4x text-primary mb-3" style="color: var(--sena-green) !important;"></i>
                    <p class="card-text">
                        Si ya tienes una cuenta en SICEFA, inicia sesión para acceder a tu dashboard 
                        personalizado según tu rol en FABRICASOFT.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-sena btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-user-lock me-2"></i>Acceso Cliente Externo</h4>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-user-lock fa-4x text-warning mb-3" style="color: var(--sena-orange) !important;"></i>
                    <p class="card-text">
                        Si tu solicitud ya fue aprobada, accede con tu correo y contraseña 
                        para ver el estado de tu proyecto.
                    </p>
                    <a href="{{ route('fabricasoft.client.login') }}" class="btn btn-sena btn-lg">
                        <i class="fas fa-user-lock me-2"></i>Acceso Cliente
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Pre-Registro</h4>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-user-plus fa-4x text-success mb-3" style="color: var(--sena-green) !important;"></i>
                    <p class="card-text">
                        ¿Eres nuevo? Solicita tu pre-registro para obtener acceso al sistema 
                        FABRICASOFT y comenzar a gestionar tus proyectos.
                    </p>
                    <a href="{{ route('fabricasoft.preregistro') }}" class="btn btn-sena btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Solicitar Acceso
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Roles -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-users-cog me-2"></i>Roles Disponibles en FABRICASOFT</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3">
                                <div class="feature-icon mb-3">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <h6 class="fw-bold">Administrador</h6>
                                <p class="small text-muted">Gestión completa del sistema, usuarios y proyectos</p>
                                <span class="badge bg-primary">Control Total</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3">
                                <div class="feature-icon mb-3">
                                    <i class="fas fa-laptop-code"></i>
                                </div>
                                <h6 class="fw-bold">Desarrollador</h6>
                                <p class="small text-muted">Gestión de tareas, código y documentación técnica</p>
                                <span class="badge bg-success">Desarrollo</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3">
                                <div class="feature-icon mb-3">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h6 class="fw-bold">Cliente Interno</h6>
                                <p class="small text-muted">Seguimiento de proyectos internos del SENA</p>
                                <span class="badge bg-info">Interno</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center p-3">
                                <div class="feature-icon mb-3">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <h6 class="fw-bold">Cliente Externo</h6>
                                <p class="small text-muted">Gestión de proyectos contratados externamente</p>
                                <span class="badge bg-warning">Externo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="text-muted mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        ¿Necesitas ayuda?
                    </h5>
                    <p class="mb-3">
                        Para soporte técnico o información adicional sobre FABRICASOFT, 
                        contacta al equipo de desarrollo del SENA.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-envelope me-1"></i>soporte@sena.edu.co
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-phone me-1"></i>+57 1 5461500
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-globe me-1"></i>www.sena.edu.co
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endauth

@endsection
