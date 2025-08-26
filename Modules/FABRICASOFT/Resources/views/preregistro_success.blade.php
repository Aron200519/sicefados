@extends('fabricasoft::layouts.app')
@section('title', 'Solicitud Enviada - FABRICASOFT')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card text-center">
            <div class="card-body p-5">
                <!-- Icono de éxito -->
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>

                <!-- Mensaje principal -->
                <h2 class="text-success mb-3">¡Solicitud de Desarrollo Enviada!</h2>
                <p class="lead text-muted mb-4">
                    Tu solicitud de desarrollo de software ha sido recibida exitosamente. Nuestro equipo técnico la revisará en las próximas 24-48 horas.
                </p>

                <!-- Información del proceso -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="feature-icon mb-2">
                                <i class="fas fa-search"></i>
                            </div>
                            <h6>Análisis Técnico</h6>
                            <p class="small text-muted">Nuestro equipo analizará tu proyecto</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="feature-icon mb-2">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h6>Contacto Directo</h6>
                            <p class="small text-muted">Te llamaremos para discutir detalles</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="feature-icon mb-2">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <h6>Propuesta</h6>
                            <p class="small text-muted">Recibirás una propuesta personalizada</p>
                        </div>
                    </div>
                </div>

                <!-- Tiempo estimado -->
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-clock"></i>
                    <strong>Tiempo estimado de respuesta:</strong> 24-48 horas
                </div>

                <!-- Próximos pasos -->
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="fas fa-list-check"></i> Próximos Pasos
                        </h5>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Revisa tu correo electrónico regularmente
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Verifica tu carpeta de spam si no recibes notificaciones
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Recibirás una llamada de nuestro equipo técnico
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-arrow-right text-primary me-2"></i>
                                Se creará una propuesta personalizada para tu proyecto
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <a href="{{ route('fabricasoft.admin.inicio') }}" class="btn btn-sena me-md-2">
                        <i class="fas fa-home"></i> Volver al Inicio
                    </a>
                    <a href="{{ route('fabricasoft.preregistro') }}" class="btn btn-sena-outline">
                        <i class="fas fa-plus"></i> Nueva Solicitud
                    </a>
                </div>

                <!-- Información de contacto -->
                <div class="mt-4 pt-3 border-top">
                    <p class="text-muted mb-2">
                        <i class="fas fa-question-circle"></i> ¿Tienes alguna pregunta?
                    </p>
                    <p class="small text-muted">
                        Puedes contactarnos en: 
                        <a href="mailto:soporte@fabricasoft.com" class="text-decoration-none">soporte@fabricasoft.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
