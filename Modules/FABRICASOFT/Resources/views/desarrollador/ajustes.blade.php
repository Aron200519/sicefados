@extends('fabricasoft::layouts.app')
@section('title', 'Ajustes del Perfil - FABRICASOFT')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog me-2"></i>Ajustes del Perfil
        </h1>
        <div>
            <a href="{{ route('fabricasoft.desarrollador.dashboard') }}" class="btn btn-sena me-2">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Mensajes de Sesión -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Información del Usuario -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Información del Perfil
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Nombre:</strong>
                                <p class="text-muted mb-0">{{ $datosPerfil['name'] ?? 'No especificado' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Email:</strong>
                                <p class="text-muted mb-0">{{ $datosPerfil['email'] ?? 'No especificado' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Teléfono:</strong>
                                <p class="text-muted mb-0">{{ $datosPerfil['phone'] ?? 'No especificado' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Organización:</strong>
                                <p class="text-muted mb-0">{{ $datosPerfil['organization'] ?? 'SENA' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información del Rol
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-success fs-6">Desarrollador</span>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Rol: Desarrollador de Software</small>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Permisos: Gestión de fases, desarrollo de código</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Ajustes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-edit me-2"></i>Editar Perfil
            </h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('fabricasoft.desarrollador.ajustes.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $datosPerfil['name'] ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $datosPerfil['email'] ?? '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $datosPerfil['phone'] ?? '') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Opcional. Formato: +57 300 123 4567</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="organization" class="form-label">Organización</label>
                            <input type="text" class="form-control" id="organization" name="organization" 
                                   value="{{ $datosPerfil['organization'] ?? 'SENA' }}" readonly>
                            <div class="form-text">SENA (no editable)</div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
