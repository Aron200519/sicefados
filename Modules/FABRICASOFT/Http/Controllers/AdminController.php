<?php

namespace Modules\FABRICASOFT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Modules\SICA\Entities\Role;
use Modules\FABRICASOFT\Entities\Preregistration;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Removido el middleware de Spatie - se maneja en las rutas
    }

    /**
     * Mostrar dashboard del admin con todas las solicitudes
     */
    public function dashboard()
    {
        // Obtener todas las solicitudes ordenadas por fecha de creación con paginación
        $solicitudes = Preregistration::orderBy('created_at', 'desc')->paginate(10);
        
        // Calcular estadísticas usando la consulta completa
        $stats = [
            'total' => Preregistration::count(),
            'pending' => Preregistration::where('status', 'pending')->count(),
            'assigned' => Preregistration::where('status', 'assigned')->count(),
            'analysis' => Preregistration::where('status', 'analysis')->count(),
            'srs_ready' => Preregistration::where('status', 'srs_ready')->count(),
            'approved' => Preregistration::where('status', 'approved')->count(),
            'rejected' => Preregistration::where('status', 'rejected')->count(),
        ];

        return view('fabricasoft::admin.dashboard', compact('solicitudes', 'stats'));
    }

    /**
     * Mostrar página de inicio con gestión de usuarios
     */
    public function inicio()
    {
        // Obtener usuarios con roles de FABRICASOFT
        $usuariosConRoles = DB::table('users')
            ->select('users.*', 'people.first_name', 'people.first_last_name', 'people.second_last_name')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.slug', 'like', 'fabricasoft.%')
            ->distinct()
            ->leftJoin('people', 'users.person_id', '=', 'people.id')
            ->orderBy('people.first_name')
            ->get();

        // Obtener clientes externos (pre-registros)
        $clientesExternos = DB::table('fabricasoft_preregistrations')
            ->select(
                'id',
                'full_name as first_name',
                'full_name as first_last_name',
                'full_name as second_last_name',
                'email',
                'created_at',
                'status'
            )
            ->where('status', '!=', 'rejected')
            ->orderBy('full_name')
            ->get();

        // Combinar ambas colecciones y agregar campo para identificar el tipo
        $usuarios = $usuariosConRoles->map(function($user) {
            $user->tipo_usuario = 'interno';
            $user->es_cliente_externo = false;
            return $user;
        });

        $clientesExternos = $clientesExternos->map(function($cliente) {
            $cliente->tipo_usuario = 'externo';
            $cliente->es_cliente_externo = true;
            $cliente->person_id = null;
            return $cliente;
        });

        // Combinar ambas colecciones
        $todosLosUsuarios = $usuarios->concat($clientesExternos)->sortBy('first_name');

        // Obtener solo los roles específicos que queremos mostrar en el modal
        $roles = \Modules\SICA\Entities\Role::whereIn('slug', [
            'fabricasoft.analista',
            'fabricasoft.desarrollador', 
            'fabricasoft.cliente_interno'
        ])->get();

        // Obtener datos para los dropdowns (EPS no existe, usar colección vacía)
        $eps = collect(); // EPS no existe, usar colección vacía
        $pensionEntities = DB::table('pension_entities')->select('id', 'name')->orderBy('name')->get();
        $populationGroups = DB::table('population_groups')->select('id', 'name')->orderBy('name')->get();

        return view('fabricasoft::admin.inicio', compact('todosLosUsuarios', 'roles', 'eps', 'pensionEntities', 'populationGroups'));
    }

    /**
     * Debug temporal para verificar roles
     */
    public function debugRoles()
    {
        $allRoles = \Modules\SICA\Entities\Role::where('slug', 'like', 'fabricasoft.%')->get(['id', 'name', 'slug']);
        
        $filteredRoles = \Modules\SICA\Entities\Role::whereIn('slug', [
            'fabricasoft.analista',
            'fabricasoft.desarrollador', 
            'fabricasoft.cliente_interno'
        ])->get(['id', 'name', 'slug']);

        return response()->json([
            'all_fabricasoft_roles' => $allRoles,
            'filtered_roles' => $filteredRoles,
            'query_used' => "whereIn('slug', ['fabricasoft.analista', 'fabricasoft.desarrollador', 'fabricasoft.cliente_interno'])"
        ]);
    }

    /**
     * Crear nuevo usuario
     */
    public function crearUsuario(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'first_last_name' => 'required|string|max:255',
            'second_last_name' => 'nullable|string|max:255',
            'document_type' => 'required|string|max:255',
            'document_number' => 'required|string|max:20',
            'eps_id' => 'nullable|string',
            'pension_entity_id' => 'nullable|integer|exists:pension_entities,id',
            'population_group_id' => 'nullable|integer|exists:population_groups,id',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            DB::beginTransaction();

            // Crear persona primero
            $personData = [
                'first_name' => $request->first_name,
                'first_last_name' => $request->first_last_name,
                'second_last_name' => $request->second_last_name,
                'document_type' => $request->document_type,
                'document_number' => $request->document_number,
                'personal_email' => $request->email,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Agregar campos obligatorios con valores por defecto si no se proporcionan
            $personData['pension_entity_id'] = $request->pension_entity_id ?? 1; // NO REGISTRA
            $personData['population_group_id'] = $request->population_group_id ?? 1; // ADOLECENTE EN CONFLICTO CON LA LEY PENAL
            
            // Agregar EPS - como la tabla no existe, usamos un valor por defecto
            $personData['eps_id'] = 1; // Valor por defecto

            $person = DB::table('people')->insertGetId($personData);

            // Crear usuario
            $usuario = \App\Models\User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'person_id' => $person,
                'nickname' => $request->first_name,
            ]);

            // Asignar rol
            $usuario->roles()->attach($request->role_id);

            DB::commit();

            Log::info('FABRICASOFT usuario creado', [
                'user_id' => $usuario->id,
                'person_id' => $person,
                'email' => $usuario->email,
                'role_id' => $request->role_id,
                'admin_id' => Auth::id(),
            ]);

            return redirect()->route('fabricasoft.admin.inicio')
                ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando usuario FABRICASOFT', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Cambiar rol de usuario
     */
    public function cambiarRol(Request $request, $userId)
    {
        $request->validate([
            'new_role_id' => 'required|exists:roles,id',
        ]);

        try {
            $usuario = \App\Models\User::findOrFail($userId);
            
            // Remover roles actuales de FABRICASOFT
            $usuario->roles()->detach(
                \Modules\SICA\Entities\Role::where('slug', 'like', 'fabricasoft.%')->pluck('id')
            );
            
            // Asignar nuevo rol
            $usuario->roles()->attach($request->new_role_id);

            Log::info('FABRICASOFT rol cambiado', [
                'user_id' => $usuario->id,
                'new_role_id' => $request->new_role_id,
                'admin_id' => Auth::id(),
            ]);

            return redirect()->route('fabricasoft.admin.inicio')
                ->with('success', 'Rol del usuario cambiado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error cambiando rol FABRICASOFT', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'Error al cambiar el rol: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar lista de todas las solicitudes
     */
    public function solicitudes()
    {
        $solicitudes = Preregistration::orderBy('created_at', 'desc')->paginate(15);
        
        // Obtener analistas para el filtro
        $analysts = \App\Models\User::whereHas('roles', function($query) {
            $query->where('slug', 'fabricasoft.analista');
        })->get();
        
        return view('fabricasoft::admin.solicitudes', compact('solicitudes', 'analysts'));
    }

    /**
     * Método de prueba para verificar el modal
     */
    public function testModal()
    {
        // Obtener lista de analistas EXCLUSIVOS (solo con rol de analista)
        $analysts = \App\Models\User::whereRaw('id IN (
            SELECT user_id FROM role_user 
            WHERE role_id IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
        )')
        ->whereRaw('id NOT IN (
            SELECT user_id FROM role_user 
            WHERE role_id NOT IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
        )')
        ->get();
        
        return view('fabricasoft::admin.test_modal', compact('analysts'));
    }

    /**
     * Mostrar detalles de una solicitud específica
     */
    public function showSolicitud($id)
    {
        $solicitud = Preregistration::findOrFail($id);
        
        // Log para debugging
        Log::info('Mostrando solicitud', [
            'solicitud_id' => $id,
            'status' => $solicitud->status,
            'assigned_analyst_id' => $solicitud->assigned_analyst_id
        ]);
        
        // Obtener lista de analistas EXCLUSIVOS (solo con rol de analista)
        $analysts = \App\Models\User::whereRaw('id IN (
            SELECT user_id FROM role_user 
            WHERE role_id IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
        )')
        ->whereRaw('id NOT IN (
            SELECT user_id FROM role_user 
            WHERE role_id NOT IN (SELECT id FROM roles WHERE slug = "fabricasoft.analista")
        )')
        ->get();
        
        // Log para debugging de analistas
        Log::info('Analistas encontrados', [
            'count' => $analysts->count(),
            'analysts' => $analysts->pluck('id', 'email')->toArray()
        ]);
        
        // Verificar si ya existe un proyecto para esta solicitud
        $existingProject = \Modules\FABRICASOFT\Entities\Project::where('preregistration_id', $solicitud->id)->first();
        
        // Log para debugging de proyecto existente
        if ($existingProject) {
            Log::info('Proyecto existente encontrado', [
                'project_id' => $existingProject->id,
                'project_name' => $existingProject->project_name
            ]);
        } else {
            Log::info('No existe proyecto previo para esta solicitud');
        }
        
        return view('fabricasoft::admin.show_solicitud', compact('solicitud', 'analysts', 'existingProject'));
    }

    /**
     * Asignar analista a una solicitud
     */
    public function assignAnalyst(Request $request, $id)
    {
        $solicitud = Preregistration::findOrFail($id);
        
        $request->validate([
            'analyst_id' => 'required|exists:users,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // Verificar que el usuario sea analista
        $analyst = User::find($request->analyst_id);
        if (!$analyst->hasCustomRole('fabricasoft.analista')) {
            return back()->withErrors(['error' => 'El usuario seleccionado no es un analista.']);
        }

        $solicitud->update([
            'assigned_analyst_id' => $request->analyst_id,
            'assigned_at' => now(),
            'workflow_status' => 'assigned',
            'analysis_status' => 'pending',
            'admin_notes' => $request->admin_notes,
        ]);

        Log::info('FABRICASOFT analista asignado', [
            'solicitud_id' => $solicitud->id,
            'analyst_id' => $request->analyst_id,
            'admin_id' => Auth::id(),
        ]);

        return redirect()->route('fabricasoft.admin.show.solicitud', $solicitud->id)
            ->with('success', 'Analista asignado exitosamente.');
    }

    /**
     * Cambiar analista asignado
     */
    public function changeAnalyst(Request $request, $id)
    {
        $solicitud = Preregistration::findOrFail($id);
        
        $request->validate([
            'new_analyst_id' => 'required|exists:users,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $newAnalyst = User::find($request->new_analyst_id);
        if (!$newAnalyst->hasCustomRole('fabricasoft.analista')) {
            return back()->withErrors(['error' => 'El usuario seleccionado no es un analista.']);
        }

        $solicitud->update([
            'assigned_analyst_id' => $request->new_analyst_id,
            'assigned_at' => now(),
            'workflow_status' => 'assigned',
            'analysis_status' => 'pending',
            'admin_notes' => $request->admin_notes,
        ]);

        Log::info('FABRICASOFT analista cambiado', [
            'solicitud_id' => $solicitud->id,
            'old_analyst_id' => $solicitud->assigned_analyst_id,
            'new_analyst_id' => $request->new_analyst_id,
            'admin_id' => Auth::id(),
        ]);

        return redirect()->route('fabricasoft.admin.show.solicitud', $solicitud->id)
            ->with('success', 'Analista cambiado exitosamente.');
    }

    /**
     * Aprobar una solicitud
     */
    public function aprobarSolicitud(Request $request, $id)
    {
        try {
            \Log::info("=== APROBANDO SOLICITUD ID: {$id} ===");
            
            $solicitud = Preregistration::findOrFail($id);
            \Log::info("Solicitud encontrada: ID={$solicitud->id}, Status={$solicitud->status}");
            
            // Verificar que la solicitud esté pendiente
            if ($solicitud->status !== 'pending') {
                return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
            }
            
            // Actualizar el estado directamente
            $solicitud->status = 'approved';
            if ($request->admin_notes) {
                $solicitud->admin_notes = $request->admin_notes;
            }
            
            $resultado = $solicitud->save();
            \Log::info("Resultado del guardado: " . ($resultado ? 'EXITOSO' : 'FALLIDO'));
            \Log::info("Nuevo status después de guardar: {$solicitud->status}");
            
            // Verificar que se guardó correctamente
            $solicitudRefreshed = Preregistration::find($id);
            \Log::info("Status después de refrescar: {$solicitudRefreshed->status}");
            
            return redirect()->route('fabricasoft.admin.dashboard')
                ->with('success', 'La solicitud ha sido aprobada exitosamente.');
                
        } catch (\Exception $e) {
            \Log::error("Error al aprobar solicitud ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Error al aprobar la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Rechazar una solicitud
     */
    public function rechazarSolicitud(Request $request, $id)
    {
        try {
            $solicitud = Preregistration::findOrFail($id);
            
            // Verificar que la solicitud esté pendiente
            if ($solicitud->status !== 'pending') {
                return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
            }
            
            // Validar que se proporcione un motivo
            $request->validate([
                'admin_notes' => 'required|string|min:10'
            ], [
                'admin_notes.required' => 'Debes proporcionar un motivo para el rechazo.',
                'admin_notes.min' => 'El motivo del rechazo debe tener al menos 10 caracteres.'
            ]);
            
            // Actualizar solo el estado y las notas del admin
            $solicitud->status = 'rejected';
            $solicitud->admin_notes = $request->admin_notes;
            $solicitud->save();
            
            return redirect()->route('fabricasoft.admin.dashboard')
                ->with('success', 'La solicitud ha sido rechazada exitosamente.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error al rechazar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Filtrar solicitudes por estado
     */
    public function filtrarSolicitudes(Request $request)
    {
        $query = Preregistration::query();
        
        // Filtro por tipo de software
        if ($request->filled('software_type')) {
            $query->where('software_type', $request->software_type);
        }
        
        // Filtro por fecha de creación
        if ($request->filled('created_date')) {
            $query->whereDate('created_at', $request->created_date);
        }
        
        // Filtro por búsqueda de texto (nombre, email, organización)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('project_description', 'like', "%{$search}%");
            });
        }
        
        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('fabricasoft::admin.solicitudes', compact('solicitudes'));
    }

    /**
     * Exportar solicitudes a CSV
     */
    public function exportarCSV()
    {
        $solicitudes = Preregistration::orderBy('created_at', 'desc')->get();
        
        $filename = 'fabricasoft_solicitudes_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($solicitudes) {
            $file = fopen('php://output', 'w');
            
            // Headers del CSV
            fputcsv($file, [
                'ID', 'Nombre', 'Email', 'Teléfono', 'Organización', 
                'Tipo de Software', 'Descripción', 'Requisitos Adicionales',
                'Estado del Flujo', 'Estado de Análisis', 'Analista Asignado',
                'SRS Subido', 'Notas Admin', 'Fecha Creación', 'Fecha Revisión'
            ]);
            
            // Datos
            foreach ($solicitudes as $solicitud) {
                fputcsv($file, [
                    $solicitud->id,
                    $solicitud->full_name,
                    $solicitud->email,
                    $solicitud->phone,
                    $solicitud->organization,
                    $solicitud->software_type,
                    $solicitud->project_description,
                    $solicitud->additional_requirements,
                    $solicitud->workflow_status_text,
                    $solicitud->analysis_status_text,
                    $solicitud->analyst ? $solicitud->analyst->name : 'No asignado',
                    $solicitud->srs_file_path ? 'Sí' : 'No',
                    $solicitud->admin_notes,
                    $solicitud->created_at->format('Y-m-d H:i:s'),
                    $solicitud->reviewed_at ? $solicitud->reviewed_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Descargar archivo SRS
     */
    public function downloadSRS($id)
    {
        try {
            Log::info("🔍 Iniciando descarga de SRS para solicitud ID: $id");
            
            $solicitud = Preregistration::findOrFail($id);
            Log::info("✅ Solicitud encontrada: " . $solicitud->full_name);
            
            if (!$solicitud->srs_file_path) {
                Log::warning("⚠️ Solicitud ID $id no tiene archivo SRS");
                abort(404, 'Archivo SRS no encontrado en la base de datos.');
            }

            Log::info("📁 Ruta del archivo SRS: " . $solicitud->srs_file_path);
            
            // Corregir la ruta del archivo - eliminar "public/" duplicado si existe
            $filePath = $solicitud->srs_file_path;
            if (str_starts_with($filePath, 'public/')) {
                $filePath = substr($filePath, 7); // Remover "public/" del inicio
            }
            
            $fullPath = storage_path('app/public/' . $filePath);
            Log::info("🔍 Ruta corregida del archivo: " . $fullPath);
            
            if (!file_exists($fullPath)) {
                Log::error("❌ Archivo no existe en el servidor: " . $fullPath);
                
                // Intentar con la ruta original como respaldo
                $originalPath = storage_path('app/' . $solicitud->srs_file_path);
                Log::info("🔄 Intentando con ruta original: " . $originalPath);
                
                if (file_exists($originalPath)) {
                    Log::info("✅ Archivo encontrado con ruta original");
                    return response()->download($originalPath);
                }
                
                abort(404, 'Archivo SRS no encontrado en el servidor: ' . $solicitud->srs_file_path);
            }

            Log::info("✅ Archivo encontrado, iniciando descarga");
            return response()->download($fullPath);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("❌ Solicitud no encontrada con ID: $id");
            abort(404, 'Solicitud no encontrada.');
        } catch (\Exception $e) {
            Log::error("❌ Error inesperado en downloadSRS: " . $e->getMessage());
            abort(500, 'Error interno del servidor al descargar el archivo.');
        }
    }

    /**
     * Editar usuario existente
     */
    public function editarUsuario(Request $request, $userId)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'first_last_name' => 'required|string|max:255',
            'second_last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'nullable|string|max:20',
            'nickname' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Obtener el usuario
            $usuario = User::findOrFail($userId);
            
            // Verificar si el email ya existe en otro usuario
            $emailExists = User::where('email', $request->email)
                ->where('id', '!=', $userId)
                ->exists();
            
            if ($emailExists) {
                return back()->withErrors(['email' => 'El correo electrónico ya está en uso por otro usuario.']);
            }

            // Actualizar datos de la persona
            if ($usuario->person_id) {
                DB::table('people')
                    ->where('id', $usuario->person_id)
                    ->update([
                        'first_name' => $request->first_name,
                        'first_last_name' => $request->first_last_name,
                        'second_last_name' => $request->second_last_name,
                        'personal_email' => $request->email,
                        'updated_at' => now(),
                    ]);
            }

            // Actualizar datos del usuario
            $usuario->update([
                'email' => $request->email,
                'phone' => $request->phone,
                'nickname' => $request->nickname,
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('fabricasoft.admin.inicio')
                ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al editar usuario: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Error al actualizar el usuario. Por favor, inténtalo de nuevo.']);
        }
    }

    /**
     * Eliminar usuario - VERSIÓN SIMPLIFICADA
     */
    public function eliminarUsuario($userId)
    {
        try {
            // Log para debugging
            Log::info("🚀 INICIANDO ELIMINACIÓN FORZADA del usuario ID: $userId");

            // Obtener el usuario
            $usuario = User::findOrFail($userId);
            Log::info("✅ Usuario encontrado: " . $usuario->email . " (Person ID: {$usuario->person_id})");

            // ELIMINACIÓN DIRECTA SIN TRANSACCIONES COMPLEJAS
            
            // 1. Eliminar roles del usuario
            $rolesEliminados = DB::table('role_user')->where('user_id', $userId)->delete();
            Log::info("🗑️ Roles eliminados: $rolesEliminados registros");
            
            // 2. Eliminar datos de la persona si existe
            if ($usuario->person_id) {
                $personaEliminada = DB::table('people')->where('id', $usuario->person_id)->delete();
                Log::info("🗑️ Persona eliminada: $personaEliminada registros (ID: {$usuario->person_id})");
            } else {
                Log::info("ℹ️ Usuario no tiene person_id asociado");
            }
            
            // 3. Eliminar el usuario PERMANENTEMENTE (forzar eliminación real)
            $usuarioEliminado = $usuario->forceDelete(); // Usar forceDelete() para eliminar realmente
            Log::info("🗑️ Usuario eliminado PERMANENTEMENTE: " . ($usuarioEliminado ? 'SÍ' : 'NO'));

            // 4. Verificar que realmente se eliminó
            $usuarioVerificado = User::find($userId);
            if ($usuarioVerificado) {
                Log::error("❌ ERROR: El usuario sigue existiendo después de eliminar!");
                return back()->withErrors(['error' => 'El usuario no se pudo eliminar completamente.']);
            } else {
                Log::info("✅ VERIFICACIÓN: Usuario eliminado correctamente de la base de datos");
            }

            return redirect()->route('fabricasoft.admin.inicio')
                ->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            Log::error('💥 ERROR CRÍTICO al eliminar usuario: ' . $e->getMessage());
            Log::error('📍 Archivo: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('📋 Stack trace: ' . $e->getTraceAsString());
            
            return back()->withErrors(['error' => 'Error al eliminar el usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * Crear o actualizar usuario del cliente externo
     */
    private function createOrUpdateExternalClient($solicitud)
    {
        try {
            // Buscar si ya existe un usuario con este email
            $user = \App\Models\User::where('email', $solicitud->email)->first();

            if (!$user) {
                // Crear nueva persona
                $person = \Modules\SICA\Entities\Person::create([
                    'first_name' => $this->extractFirstName($solicitud->full_name),
                    'first_last_name' => $this->extractLastName($solicitud->full_name),
                    'personal_email' => $solicitud->email,
                    'telephone1' => $solicitud->phone,
                    'document_type' => 'Cédula de ciudadanía', // Por defecto
                    'document_number' => 'TEMP_' . time(), // Temporal, se debe actualizar después
                ]);

                // Crear nuevo usuario
                $user = \App\Models\User::create([
                    'email' => $solicitud->email,
                    'password' => $solicitud->password, // Ya está hasheada
                    'phone' => $solicitud->phone,
                    'nickname' => $solicitud->full_name,
                    'person_id' => $person->id,
                ]);

                Log::info('Usuario del cliente externo creado', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'preregistration_id' => $solicitud->id
                ]);
            }

            // Asignar rol de cliente externo
            $this->assignExternalClientRole($user);

        } catch (\Exception $e) {
            Log::error('Error al crear usuario del cliente externo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extraer el primer nombre del nombre completo
     */
    private function extractFirstName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? '';
    }

    /**
     * Extraer el apellido del nombre completo
     */
    private function extractLastName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        if (count($parts) > 1) {
            return implode(' ', array_slice($parts, 1));
        }
        return '';
    }

    /**
     * Asignar rol de cliente externo al usuario
     */
    private function assignExternalClientRole($user)
    {
        try {
            // Buscar el rol de cliente externo
            $role = \Modules\SICA\Entities\Role::where('slug', 'fabricasoft.cliente_externo')->first();
            
            if ($role && !$user->hasCustomRole('fabricasoft.cliente_externo')) {
                $user->roles()->attach($role->id);
                
                Log::info('Rol de cliente externo asignado al usuario', [
                    'user_id' => $user->id,
                    'role_id' => $role->id
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('No se pudo asignar el rol de cliente externo', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
