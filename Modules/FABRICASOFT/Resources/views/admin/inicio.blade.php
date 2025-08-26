@extends('fabricasoft::layouts.app')
@section('title', 'Inicio - Gestión de Usuarios - FABRICASOFT')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-home me-2"></i>Gestión de Usuarios FABRICASOFT
        </h1>
        <div>
            <button type="button" class="btn btn-sena" onclick="mostrarModalCrearUsuario()">
                <i class="fas fa-user-plus me-2"></i>Agregar Nuevo Usuario
            </button>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Usuarios
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todosLosUsuarios->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Analistas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $analistasCount = DB::table('users')
                                        ->join('role_user', 'users.id', '=', 'role_user.user_id')
                                        ->join('roles', 'role_user.role_id', '=', 'roles.id')
                                        ->where('roles.slug', 'fabricasoft.analista')
                                        ->distinct()
                                        ->count('users.id');
                                    echo $analistasCount;
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Desarrolladores
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $desarrolladoresCount = DB::table('users')
                                        ->join('role_user', 'users.id', '=', 'role_user.user_id')
                                        ->join('roles', 'role_user.role_id', '=', 'roles.id')
                                        ->where('roles.slug', 'fabricasoft.desarrollador')
                                        ->distinct()
                                        ->count('users.id');
                                    echo $desarrolladoresCount;
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-code fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Clientes Internos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $clientesInternosCount = DB::table('users')
                                        ->join('role_user', 'users.id', '=', 'role_user.user_id')
                                        ->join('roles', 'role_user.role_id', '=', 'roles.id')
                                        ->where('roles.slug', 'fabricasoft.cliente_interno')
                                        ->distinct()
                                        ->count('users.id');
                                    echo $clientesInternosCount;
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Clientes Externos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $clientesExternosCount = DB::table('fabricasoft_preregistrations')
                                        ->where('status', '!=', 'rejected')
                                        ->count();
                                    echo $clientesExternosCount;
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>Usuarios FABRICASOFT
                <span class="badge bg-primary ms-2">{{ $todosLosUsuarios->count() }}</span>
            </h6>
        </div>
        <div class="card-body">
            @if($todosLosUsuarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Información del Usuario</th>
                                <th>Rol Actual</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todosLosUsuarios as $usuario)
                            <tr>
                                <td>
                                    <strong>#{{ $usuario->id }}</strong>
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <strong>
                                            @if($usuario->first_name && $usuario->first_last_name)
                                                {{ $usuario->first_name }} {{ $usuario->first_last_name }}
                                                @if($usuario->second_last_name)
                                                    {{ $usuario->second_last_name }}
                                                @endif
                                            @else
                                                {{ $usuario->nickname ?? 'Sin nombre' }}
                                            @endif
                                        </strong>
                                    </div>
                                    <div class="mb-1">
                                        <i class="fas fa-envelope me-1 text-muted"></i>
                                        <a href="mailto:{{ $usuario->email }}" class="text-decoration-none">
                                            {{ $usuario->email }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    @if($usuario->es_cliente_externo)
                                        <!-- Cliente Externo -->
                                        <span class="badge bg-danger fs-6 me-1 mb-1">Cliente Externo</span>
                                        <span class="badge bg-info fs-6 me-1 mb-1">{{ ucfirst($usuario->status ?? 'Pendiente') }}</span>
                                    @else
                                        <!-- Usuario Interno -->
                                        @php
                                            // Obtener TODOS los roles de FABRICASOFT del usuario
                                            $roles = DB::table('users')
                                                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                                                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                                                ->where('users.id', $usuario->id)
                                                ->where('roles.slug', 'like', 'fabricasoft.%')
                                                ->select('roles.name', 'roles.slug', 'roles.id')
                                                ->orderBy('roles.slug')
                                                ->get();
                                        @endphp
                                        
                                        @if($roles->count() > 0)
                                            @foreach($roles as $rol)
                                                @php
                                                    $rolClass = '';
                                                    $rolText = '';
                                                    switch($rol->slug) {
                                                        case 'fabricasoft.analista':
                                                            $rolClass = 'bg-success';
                                                            $rolText = 'Analista';
                                                            break;
                                                        case 'fabricasoft.desarrollador':
                                                            $rolClass = 'bg-info';
                                                            $rolText = 'Desarrollador';
                                                            break;
                                                        case 'fabricasoft.cliente_interno':
                                                            $rolClass = 'bg-warning';
                                                            $rolText = 'Cliente Interno';
                                                            break;
                                                        case 'fabricasoft.admin':
                                                            $rolClass = 'bg-danger';
                                                            $rolText = 'Admin';
                                                            break;
                                                        case 'fabricasoft.aprendiz':
                                                            $rolClass = 'bg-primary';
                                                            $rolText = 'Aprendiz';
                                                            break;
                                                        case 'fabricasoft.users':
                                                            $rolClass = 'bg-secondary';
                                                            $rolText = 'Usuario';
                                                            break;
                                                        default:
                                                            $rolClass = 'bg-secondary';
                                                            $rolText = $rol->name;
                                                    }
                                                @endphp
                                                <span class="badge {{ $rolClass }} fs-6 me-1 mb-1">{{ $rolText }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary">Sin Rol</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <strong>Creado:</strong><br>
                                        <small>
                                            @if($usuario->created_at)
                                                {{ \Carbon\Carbon::parse($usuario->created_at)->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">No disponible</span>
                                            @endif
                                        </small>
                                    </div>
                                    @if(!$usuario->es_cliente_externo && isset($usuario->updated_at) && $usuario->updated_at != $usuario->created_at)
                                    <div>
                                        <strong>Actualizado:</strong><br>
                                        <small>{{ \Carbon\Carbon::parse($usuario->updated_at)->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary mb-1" 
                                                onclick="mostrarModalEditarUsuario({{ $usuario->id }}, '{{ $usuario->first_name ?? '' }}', '{{ $usuario->first_last_name ?? '' }}', '{{ $usuario->second_last_name ?? '' }}', '{{ $usuario->email ?? '' }}', '{{ $usuario->phone ?? '' }}', '{{ $usuario->nickname ?? '' }}')" 
                                                title="Editar Usuario">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <form method="POST" action="{{ route('fabricasoft.admin.usuarios.eliminar', $usuario->id) }}" 
                                              style="display: inline-block;" 
                                              onsubmit="return confirm('¿Estás SEGURO de que deseas eliminar al usuario {{ $usuario->first_name && $usuario->first_last_name ? $usuario->first_name . ' ' . $usuario->first_last_name : ($usuario->nickname ?? 'Usuario') }}?\n\n⚠️ Esta acción NO se puede deshacer.\n\nSe eliminarán todos los datos asociados.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger mb-1" title="Eliminar Usuario">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-gray-300 mb-3"></i>
                    <h4 class="text-gray-500">No hay usuarios registrados</h4>
                    <p class="text-gray-400">Comienza agregando el primer usuario al sistema.</p>
                    <button type="button" class="btn btn-sena" onclick="mostrarModalCrearUsuario()">
                        <i class="fas fa-user-plus me-2"></i>Agregar Usuario
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearUsuarioLabel">Crear Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('fabricasoft.admin.usuarios.crear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Crea un nuevo usuario y asígnale un rol específico en el sistema FABRICASOFT.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">Primer Nombre *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       placeholder="Primer nombre" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="first_last_name" class="form-label">Primer Apellido *</label>
                                <input type="text" class="form-control" id="first_last_name" name="first_last_name" 
                                       placeholder="Primer apellido" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="second_last_name" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="second_last_name" name="second_last_name" 
                                       placeholder="Segundo apellido (opcional)">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="document_type" class="form-label">Tipo de Documento *</label>
                                <select class="form-select" id="document_type" name="document_type" required>
                                    <option value="">Selecciona tipo</option>
                                    <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                                    <option value="Tarjeta de identidad">Tarjeta de identidad</option>
                                    <option value="Cédula de extranjería">Cédula de extranjería</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Documento nacional de identidad">Documento nacional de identidad</option>
                                    <option value="Registro civil">Registro civil</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="document_number" class="form-label">Número de Documento *</label>
                                <input type="text" class="form-control" id="document_number" name="document_number" 
                                       placeholder="CC o TI" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Celular *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="300 123 4567" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="eps_id" class="form-label">EPS (opcional)</label>
                                <input type="text" class="form-control" id="eps_id" name="eps_id" 
                                       placeholder="Nombre de la EPS (opcional)">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="pension_entity_id" class="form-label">Fondo de Pensiones (opcional)</label>
                                <select class="form-select" id="pension_entity_id" name="pension_entity_id">
                                    <option value="">Selecciona fondo (opcional)</option>
                                    @if($pensionEntities && $pensionEntities->count() > 0)
                                        @foreach($pensionEntities as $pension)
                                            <option value="{{ $pension->id }}">{{ $pension->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No hay fondos disponibles</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="population_group_id" class="form-label">Grupo Poblacional (opcional)</label>
                                <select class="form-select" id="population_group_id" name="population_group_id">
                                    <option value="">Selecciona grupo (opcional)</option>
                                    @if($populationGroups && $populationGroups->count() > 0)
                                        @foreach($populationGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No hay grupos disponibles</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="usuario@ejemplo.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Mínimo 8 caracteres" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                                       placeholder="Repite la contraseña" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Rol del Usuario *</label>
                                
                                                @php
                                    // Obtener roles específicos directamente
                                    $rolesDirectos = \Modules\SICA\Entities\Role::whereIn('slug', [
                                        'fabricasoft.analista',
                                        'fabricasoft.desarrollador', 
                                        'fabricasoft.cliente_interno'
                                    ])->get();
                                @endphp
                                
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <option value="">Selecciona un rol</option>
                                    @if($rolesDirectos && $rolesDirectos->count() > 0)
                                        @foreach($rolesDirectos as $rol)
                                            @php
                                                $rolText = '';
                                                switch($rol->slug) {
                                                    case 'fabricasoft.analista':
                                                        $rolText = 'Analista - Revisa solicitudes y crea SRS';
                                                        break;
                                                    case 'fabricasoft.desarrollador':
                                                        $rolText = 'Desarrollador - Implementa funcionalidades';
                                                        break;
                                                    case 'fabricasoft.cliente_interno':
                                                        $rolText = 'Cliente Interno - Solicita desarrollos';
                                                        break;
                                                    default:
                                                        $rolText = $rol->name;
                                                }
                                            @endphp
                                            <option value="{{ $rol->id }}">{{ $rolText }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No hay roles disponibles</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sena">
                        <i class="fas fa-user-plus me-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarUsuario" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Modifica la información del usuario seleccionado.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_first_name" class="form-label">Primer Nombre *</label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name" 
                                       placeholder="Primer nombre" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_first_last_name" class="form-label">Primer Apellido *</label>
                                <input type="text" class="form-control" id="edit_first_last_name" name="first_last_name" 
                                       placeholder="Primer apellido" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_second_last_name" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="edit_second_last_name" name="second_last_name" 
                                       placeholder="Segundo apellido (opcional)">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Correo Electrónico *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" 
                                       placeholder="correo@ejemplo.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_phone" class="form-label">Celular *</label>
                                <input type="tel" class="form-control" id="edit_phone" name="phone" 
                                       placeholder="300 123 4567" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nickname" class="form-label">Nickname</label>
                                <input type="text" class="form-control" id="edit_nickname" name="nickname" 
                                       placeholder="Nickname (opcional)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function mostrarModalCrearUsuario() {
    const modal = new bootstrap.Modal(document.getElementById('modalCrearUsuario'));
    modal.show();
}

function mostrarModalEditarUsuario(userId, firstName, firstLastName, secondLastName, email, phone, nickname) {
    console.log('🔍 Función mostrarModalEditarUsuario llamada con:', { userId, firstName, firstLastName, secondLastName, email, phone, nickname });
    
    try {
        // Llenar el formulario con los datos actuales del usuario
        document.getElementById('edit_first_name').value = firstName || '';
        document.getElementById('edit_first_last_name').value = firstLastName || '';
        document.getElementById('edit_second_last_name').value = secondLastName || '';
        document.getElementById('edit_email').value = email || '';
        document.getElementById('edit_phone').value = phone || '';
        document.getElementById('edit_nickname').value = nickname || '';
        
        console.log('✅ Formulario llenado correctamente');
        
        // Configurar la acción del formulario
        const form = document.getElementById('formEditarUsuario');
        form.action = `/fabricasoft/admin/usuarios/${userId}/editar`;
        
        console.log('✅ Acción del formulario configurada:', form.action);
        
        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
        modal.show();
        
        console.log('✅ Modal mostrado correctamente');
        
    } catch (error) {
        console.error('❌ Error en mostrarModalEditarUsuario:', error);
        alert('Error al abrir el modal de edición: ' + error.message);
    }
}


</script>
@endpush
