<?php

namespace Modules\FABRICASOFT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added this import for DB facade
use Illuminate\Support\Facades\Log; // Added this import for Log facade
use Illuminate\Support\Facades\Hash; // Added this import for Hash facade

class FABRICASOFTController extends Controller
{
    /**
     * Vista principal del módulo (accesible sin autenticación)
     * Si el usuario está autenticado, lo redirige a su dashboard
     */
    public function index()
    {
        // Si el usuario está autenticado, redirigirlo a su dashboard
        if (Auth::check()) {
            $user = Auth::user();
            
            // Debug temporal - mostrar información del usuario
            if (request()->has('debug')) {
                dd([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_roles' => $user->roles->pluck('name', 'slug')->toArray(),
                    'has_role_fabricasoft_admin' => $user->hasCustomRole('fabricasoft.admin'),
                    'has_role_fabricasoft_desarrollador' => $user->hasCustomRole('fabricasoft.desarrollador'),
                    'has_role_fabricasoft_cliente_interno' => $user->hasCustomRole('fabricasoft.cliente_interno'),
                    'has_role_fabricasoft_cliente_externo' => $user->hasCustomRole('fabricasoft.cliente_externo'),
                ]);
            }
            
            // Verificar roles usando nuestro método personalizado
            $roles = [
                'has_role_fabricasoft_admin' => $user->hasCustomRole('fabricasoft.admin'),
                'has_role_fabricasoft_analista' => $user->hasCustomRole('fabricasoft.analista'),
                'has_role_fabricasoft_desarrollador' => $user->hasCustomRole('fabricasoft.desarrollador'),
                'has_role_fabricasoft_cliente_interno' => $user->hasCustomRole('fabricasoft.cliente_interno'),
                'has_role_fabricasoft_cliente_externo' => $user->hasCustomRole('fabricasoft.cliente_externo'),
            ];
            
            // Redirigir según el rol
            if ($user->hasCustomRole('fabricasoft.admin')) {
                return redirect()->route('fabricasoft.admin.dashboard');
            } elseif ($user->hasCustomRole('fabricasoft.analista')) {
                return redirect()->route('fabricasoft.analyst.dashboard');
            } elseif ($user->hasCustomRole('fabricasoft.desarrollador')) {
                return redirect()->route('fabricasoft.desarrollador.dashboard');
            } elseif ($user->hasCustomRole('fabricasoft.cliente_interno')) {
                return redirect()->route('fabricasoft.cliente_interno.dashboard');
            } elseif ($user->hasCustomRole('fabricasoft.cliente_externo')) {
                return redirect()->route('cliente_externo.dashboard');
            }
            
            // Si no tiene ningún rol específico en FABRICASOFT, mostrar mensaje
            return view('fabricasoft::index')->with('no_role_message', 'No tienes un rol asignado en FABRICASOFT. Contacta al administrador.');
        }
        
        // Si no está autenticado, mostrar la página de bienvenida
        return view('fabricasoft::index');
    }

    /**
     * Dashboard del administrador
     */
    public function adminDashboard()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Acceso denegado. Solo administradores pueden acceder a esta sección.');
        }
        
        return redirect()->route('fabricasoft.admin.dashboard');
    }
    
    /**
     * Dashboard del analista
     */
    public function analystDashboard()
    {
        return redirect()->route('fabricasoft.analyst.dashboard');
    }

    /**
     * Dashboard del desarrollador
     */
    public function desarrolladorDashboard()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.desarrollador')) {
            abort(403, 'Acceso denegado. Solo desarrolladores pueden acceder a esta sección.');
        }
        
        $developerId = Auth::id();
        
        // Obtener proyectos asignados al desarrollador
        $projects = \Modules\FABRICASOFT\Entities\Project::whereHas('teamMembers', function($query) use ($developerId) {
            $query->where('user_id', $developerId)
                  ->where('status', 'active');
        })->with(['phases', 'teamMembers', 'preregistration', 'scrumMaster'])
          ->orderBy('created_at', 'desc')
          ->get();
        
        // Calcular estadísticas generales
        $stats = [
            'total_projects' => $projects->count(),
            'active_projects' => $projects->where('status', 'active')->count(),
            'completed_projects' => $projects->where('status', 'completed')->count(),
            'total_phases' => $projects->sum(function($project) {
                return $project->phases->count();
            }),
            'completed_phases' => $projects->sum(function($project) {
                return $project->phases->where('status', 'completed')->count();
            }),
            'in_progress_phases' => $projects->sum(function($project) {
                return $project->phases->where('status', 'in_progress')->count();
            }),
            'planned_phases' => $projects->sum(function($project) {
                return $project->phases->where('status', 'planned')->count();
            })
        ];
        
        // Calcular progreso general
        $stats['overall_progress'] = $stats['total_phases'] > 0 
            ? round(($stats['completed_phases'] / $stats['total_phases']) * 100, 1)
            : 0;
        
        // Obtener actividad reciente (últimas 8 entradas)
        $recentActivity = \Modules\FABRICASOFT\Entities\PhaseInfoHistory::whereHas('project.teamMembers', function($query) use ($developerId) {
            $query->where('user_id', $developerId)
                  ->where('status', 'active');
        })->with(['project', 'phase'])
          ->orderBy('created_at', 'desc')
          ->limit(8)
          ->get();
        
        // Proyectos con mejor progreso (top 3)
        $topProjects = $projects->sortByDesc(function($project) {
            $totalPhases = $project->phases->count();
            $completedPhases = $project->phases->where('status', 'completed')->count();
            return $totalPhases > 0 ? ($completedPhases / $totalPhases) * 100 : 0;
        })->take(3);
        
        // Fases próximas a vencer (en los próximos 7 días)
        $upcomingDeadlines = collect();
        foreach ($projects as $project) {
            foreach ($project->phases as $phase) {
                if ($phase->end_date && $phase->status !== 'completed') {
                    $daysUntilDeadline = now()->diffInDays($phase->end_date, false);
                    if ($daysUntilDeadline >= 0 && $daysUntilDeadline <= 7) {
                        $upcomingDeadlines->push([
                            'phase' => $phase,
                            'project' => $project,
                            'days_until' => $daysUntilDeadline
                        ]);
                    }
                }
            }
        }
        $upcomingDeadlines = $upcomingDeadlines->sortBy('days_until')->take(5);
        
        // Fases asignadas al desarrollador
        $myAssignedPhases = collect();
        foreach ($projects as $project) {
            foreach ($project->phases as $phase) {
                if ($phase->assigned_to === $developerId && $phase->status !== 'completed') {
                    $myAssignedPhases->push([
                        'phase' => $phase,
                        'project' => $project
                    ]);
                }
            }
        }
        $myAssignedPhases = $myAssignedPhases->sortBy('phase.end_date')->take(6);
        
        return view('fabricasoft::desarrollador.dashboard', compact(
            'projects', 
            'stats', 
            'recentActivity', 
            'topProjects',
            'upcomingDeadlines',
            'myAssignedPhases'
        ));
    }
    
    /**
     * Dashboard del cliente interno
     */
    public function clienteInternoDashboard()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_interno')) {
            abort(403, 'Acceso denegado. Solo clientes internos pueden acceder a esta sección.');
        }

        $user = Auth::user();
        
        // Obtener todas las solicitudes del cliente interno (pendientes, aprobadas, rechazadas)
        $todasLasSolicitudes = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Obtener las solicitudes aprobadas del cliente interno
        $solicitudesAprobadas = $todasLasSolicitudes->where('status', 'approved');

        // Obtener proyectos creados a partir de las solicitudes aprobadas
        $proyectos = \Modules\FABRICASOFT\Entities\Project::whereIn('preregistration_id', $solicitudesAprobadas->pluck('id'))
            ->with(['preregistration', 'phases', 'teamMembers', 'scrumMaster'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estadísticas
        $stats = [
            'total_proyectos' => $proyectos->count(),
            'proyectos_activos' => $proyectos->where('status', 'active')->count(),
            'proyectos_completados' => $proyectos->where('status', 'completed')->count(),
            'proyectos_pendientes' => $proyectos->where('status', 'pending')->count(),
            'total_fases' => $proyectos->sum(function($proyecto) {
                return $proyecto->phases->count();
            }),
        ];

        return view('fabricasoft::cliente_interno.dashboard', compact('proyectos', 'solicitudesAprobadas', 'todasLasSolicitudes', 'stats'));
    }
    
    /**
     * Dashboard del cliente externo
     */
    public function clienteExternoDashboard()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_externo')) {
            abort(403, 'Acceso denegado. Solo clientes externos pueden acceder a esta sección.');
        }

        $user = Auth::user();
        
        // Obtener todas las solicitudes del cliente externo (pendientes, aprobadas, rechazadas)
        $todasLasSolicitudes = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Obtener las solicitudes aprobadas del cliente externo
        $solicitudesAprobadas = $todasLasSolicitudes->where('status', 'approved');

        // Obtener proyectos creados a partir de las solicitudes aprobadas
        $proyectos = \Modules\FABRICASOFT\Entities\Project::whereIn('preregistration_id', $solicitudesAprobadas->pluck('id'))
            ->with(['preregistration', 'phases', 'teamMembers'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular estadísticas
        $stats = [
            'total_proyectos' => $proyectos->count(),
            'proyectos_activos' => $proyectos->where('status', 'active')->count(),
            'proyectos_completados' => $proyectos->where('status', 'completed')->count(),
            'proyectos_pendientes' => $proyectos->where('status', 'pending')->count(),
        ];

        return view('fabricasoft::cliente_externo.dashboard', compact('proyectos', 'solicitudesAprobadas', 'todasLasSolicitudes', 'stats'));
    }
    
    /**
     * Equipos Scrum asignados al desarrollador
     */
    public function desarrolladorProjects()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.desarrollador')) {
            abort(403, 'Acceso denegado. Solo desarrolladores pueden acceder a esta sección.');
        }
        
        $developerId = Auth::id();
        
        // Buscar proyectos donde el desarrollador sea miembro del equipo
        $projects = \Modules\FABRICASOFT\Entities\Project::whereHas('teamMembers', function($query) use ($developerId) {
            $query->where('user_id', $developerId)
                  ->where('status', 'active');
        })->with(['preregistration', 'phases', 'teamMembers'])
          ->orderBy('created_at', 'desc')
          ->get();
        
        // Calcular estadísticas
        $stats = [
            'total_projects' => $projects->count(),
            'in_development' => $projects->where('status', 'in_progress')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'completed_phases' => $projects->sum(function($project) {
                return $project->phases->where('status', 'completed')->count();
            })
        ];
        
        return view('fabricasoft::desarrollador.projects', compact('projects', 'stats'));
    }

    /**
     * Mostrar lista de proyectos para cliente externo
     */
    public function clienteExternoProjects()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_externo')) {
            abort(403, 'Acceso denegado. Solo clientes externos pueden acceder a esta sección.');
        }

        $user = Auth::user();
        
        // Primero verificar si el usuario tiene un preregistration asociado
        $preregistration = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)->first();
        
        if (!$preregistration) {
            // Si no tiene preregistration, mostrar mensaje y no proyectos
            $proyectos = collect();
            return view('fabricasoft::cliente_externo.projects', compact('proyectos', 'preregistration'));
        }
        
        // Obtener proyectos únicos del cliente externo usando whereHas para evitar duplicados
        $proyectos = \Modules\FABRICASOFT\Entities\Project::whereHas('preregistration', function($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->with(['preregistration', 'phases', 'teamMembers', 'scrumMaster'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('fabricasoft::cliente_externo.projects', compact('proyectos', 'preregistration'));
    }

    /**
     * Mostrar proyecto para cliente externo
     */
    public function showProjectForClient($id)
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_externo')) {
            abort(403, 'Acceso denegado. Solo clientes externos pueden acceder a esta sección.');
        }
        
        $user = Auth::user();
        
        $proyecto = \Modules\FABRICASOFT\Entities\Project::where('id', $id)
            ->whereHas('preregistration', function($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->with(['preregistration', 'phases', 'teamMembers.user', 'scrumMaster'])
            ->firstOrFail();
            
        // Cargar el historial de información de las fases con la relación del usuario
        foreach ($proyecto->phases as $phase) {
            $phase->phaseInfoHistory = DB::table('fabricasoft_phase_info_history')
                ->where('project_id', $id)
                ->where('phase_id', $phase->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($item) {
                    // Convertir a objeto y agregar la relación del usuario si existe
                    $item = (object) $item;
                    if (isset($item->added_by) && $item->added_by) {
                        $item->addedByUser = \App\Models\User::find($item->added_by);
                    } else {
                        $item->addedByUser = null;
                    }
                    return $item;
                });
        }
        
        // Cargar desarrolladores si existe la relación
        if (method_exists($proyecto, 'developers')) {
            $proyecto->load('developers');
        } else {
            $proyecto->developers = collect();
        }
        
        return view('fabricasoft::cliente_externo.show_project', compact('proyecto'));
    }
    


    public function nuevaSolicitudForm()
    {
        Log::info("=== INICIO nuevaSolicitudForm ===");
        
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_externo')) {
            Log::error("Usuario no tiene rol de cliente externo");
            abort(403, 'Acceso denegado. Solo clientes externos pueden acceder a esta sección.');
        }
        
        $user = Auth::user();
        Log::info("Usuario autenticado: " . $user->email);
        
        // Verificar si el usuario ya tiene un proyecto activo
        $proyectoActivo = \Modules\FABRICASOFT\Entities\Project::whereHas('preregistration', function($query) use ($user) {
            $query->where('email', $user->email);
        })->whereIn('status', ['planning', 'active'])->first();
        

        
        try {
            // Obtener la información del cliente desde la tabla de personas
            $persona = null;
            if ($user->person_id) {
                $persona = \Modules\SICA\Entities\Person::where('id', $user->person_id)->first();
                Log::info("Persona encontrada para usuario {$user->id}: " . ($persona ? $persona->id : 'null'));
            } else {
                Log::info("Usuario {$user->id} no tiene person_id asociado");
            }
            
            // Obtener el pre-registro existente para pre-llenar datos
            $preregistroExistente = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
                ->orderBy('created_at', 'desc')
                ->first();
            
            Log::info("Pre-registro encontrado para email {$user->email}: " . ($preregistroExistente ? $preregistroExistente->id : 'null'));
            Log::info("Datos del usuario: ID={$user->id}, Name='{$user->name}', Email='{$user->email}', PersonID=" . ($user->person_id ?? 'null'));
            
            // Si no hay persona, crear un objeto vacío para evitar errores
            if (!$persona) {
                $persona = (object) [
                    'phone' => $user->phone, // Usar el teléfono del usuario si está disponible
                    'organization' => null,
                    'full_name' => $user->nickname // Agregar el nombre del usuario usando nickname
                ];
            } else {
                // Si hay persona, usar su teléfono o el del usuario
                $persona->phone = $persona->telephone1 ?? $persona->telephone2 ?? $persona->telephone3 ?? $user->phone;
                // La tabla people no tiene campo organization, se obtiene del pre-registro
                $persona->organization = null;
                // Construir el nombre completo desde la tabla people
                $persona->full_name = trim($persona->first_name . ' ' . $persona->first_last_name . ' ' . ($persona->second_last_name ?? ''));
                // Si no hay nombre en people, usar el del usuario
                if (empty($persona->full_name)) {
                    $persona->full_name = $user->nickname ?? 'Usuario';
                }
            }
            
            // Si no hay pre-registro previo, crear un objeto vacío
            if (!$preregistroExistente) {
                $preregistroExistente = (object) [
                    'phone' => null,
                    'organization' => null
                ];
            } else {
                // Asegurar que el pre-registro tenga los campos necesarios
                $preregistroExistente->phone = $preregistroExistente->phone ?? null;
                $preregistroExistente->organization = $preregistroExistente->organization ?? null;
            }
            
            Log::info("Retornando vista nueva_solicitud");
            return view('fabricasoft::cliente_externo.nueva_solicitud', compact('user', 'persona', 'preregistroExistente', 'proyectoActivo'));
            
        } catch (\Exception $e) {
            Log::error("Error en nuevaSolicitudForm: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            // En caso de error, crear objetos vacíos y continuar
            $persona = (object) [
                'phone' => $user->phone ?? null,
                'organization' => null,
                'full_name' => $user->nickname ?? 'Usuario'
            ];
            
            $preregistroExistente = (object) [
                'phone' => null,
                'organization' => null
            ];
            
            return view('fabricasoft::cliente_externo.nueva_solicitud', compact('user', 'persona', 'preregistroExistente', 'proyectoActivo'));
        }
    }
    
    public function guardarNuevaSolicitud(Request $request)
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_externo')) {
            abort(403, 'Acceso denegado. Solo clientes externos pueden acceder a esta sección.');
        }
        
        $user = Auth::user();
        
        // Verificar si el usuario ya tiene un proyecto activo
        $proyectoActivo = \Modules\FABRICASOFT\Entities\Project::whereHas('preregistration', function($query) use ($user) {
            $query->where('email', $user->email);
        })->whereIn('status', ['planning', 'active'])->first();
        
        if ($proyectoActivo) {
            // Si tiene un proyecto activo, mostrar mensaje de error pero permitir ver la vista
            return redirect()->route('cliente_externo.nueva-solicitud')
                ->with('error', 'No puedes crear una nueva solicitud mientras tengas un proyecto activo. Tu proyecto "' . $proyectoActivo->project_name . '" está en estado: ' . $proyectoActivo->status_text);
        }
        
        // Validar los datos del formulario
        $request->validate([
            'project_description' => 'required|string|max:1000',
            'software_type' => 'required|string|in:aplicacion_web,aplicacion_movil,sistema_desktop,base_datos,api,otro',
            'additional_requirements' => 'nullable|string|max:1000',
            'organization' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Crear el nuevo pre-registro
            $preregistro = new \Modules\FABRICASOFT\Entities\Preregistration();
            $preregistro->full_name = $user->name;
            $preregistro->email = $user->email;
            $preregistro->phone = $request->phone;
            $preregistro->organization = $request->organization;
            $preregistro->project_description = $request->project_description;
            $preregistro->software_type = $request->software_type;
            $preregistro->additional_requirements = $request->additional_requirements;
            $preregistro->client_type = 'cliente_externo';
            $preregistro->status = 'pending';
            $preregistro->workflow_status = 'pending';
            $preregistro->save();
            
            DB::commit();
            
            return redirect()->route('cliente_externo.dashboard')
                ->with('success', 'Tu nueva solicitud ha sido enviada exitosamente. Será revisada por nuestro equipo.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear nueva solicitud: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Error al enviar la solicitud. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Mostrar formulario de ajustes del perfil
     */
    public function ajustesPerfil()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_externo')) {
            abort(403, 'Acceso denegado. Solo clientes externos pueden acceder a esta sección.');
        }
        
        $user = Auth::user();
        
        try {
            // Obtener la información del cliente desde la tabla de personas
            $persona = null;
            if ($user->person_id) {
                $persona = \Modules\SICA\Entities\Person::where('id', $user->person_id)->first();
                Log::info("Persona encontrada para ajustes del usuario {$user->id}: " . ($persona ? $persona->id : 'null'));
            }
            
            // Obtener el pre-registro existente para pre-llenar datos
            $preregistroExistente = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
                ->orderBy('created_at', 'desc')
                ->first();
            
            Log::info("Pre-registro encontrado para ajustes del email {$user->email}: " . ($preregistroExistente ? $preregistroExistente->id : 'null'));
            
            // Preparar datos para la vista
            $datosPerfil = [
                'name' => $user->nickname,
                'email' => $user->email,
                'phone' => $persona ? ($persona->telephone1 ?? $persona->telephone2 ?? $persona->telephone3 ?? $user->phone) : $user->phone,
                'organization' => $preregistroExistente ? $preregistroExistente->organization : null,
                'person_id' => $user->person_id,
                'preregistro_id' => $preregistroExistente ? $preregistroExistente->id : null
            ];
            
            return view('fabricasoft::cliente_externo.ajustes', compact('user', 'persona', 'preregistroExistente', 'datosPerfil'));
            
        } catch (\Exception $e) {
            Log::error("Error en ajustesPerfil: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            // En caso de error, crear datos básicos y continuar
            $datosPerfil = [
                'name' => $user->nickname ?? 'Usuario',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'organization' => null,
                'person_id' => $user->person_id,
                'preregistro_id' => null
            ];
            
            return view('fabricasoft::cliente_externo.ajustes', compact('user', 'persona', 'preregistroExistente', 'datosPerfil'));
        }
    }
    
    /**
     * Actualizar información del perfil
     */
    public function actualizarPerfil(Request $request)
    {
        Log::info("=== INICIO actualizarPerfil ===");
        Log::info("Usuario autenticado: " . Auth::id());
        Log::info("Datos recibidos: " . json_encode($request->all()));
        Log::info("Método HTTP: " . $request->method());
        Log::info("URL: " . $request->url());
        Log::info("Headers: " . json_encode($request->headers->all()));
        
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_externo')) {
            Log::error("Usuario no tiene rol de cliente externo");
            abort(403, 'Acceso denegado. Solo clientes externos pueden acceder a esta sección.');
        }
        
        Log::info("Usuario tiene rol correcto, continuando...");
        
        $user = Auth::user();
        
        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'organization.max' => 'El nombre de la empresa no puede tener más de 255 caracteres.',
            'current_password.required_with' => 'Debes ingresar tu contraseña actual para cambiarla.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);
        
        Log::info("Validación pasada correctamente");
        
        try {
            DB::beginTransaction();
            Log::info("Transacción iniciada");
            
            // Actualizar nombre del usuario
            $user->nickname = $request->name;
            Log::info("Nickname actualizado a: " . $request->name);
            
            // Actualizar teléfono del usuario
            $user->phone = $request->phone;
            Log::info("Teléfono actualizado a: " . $request->phone);
            
            // Si se está cambiando la contraseña, validar y actualizar
            if ($request->filled('new_password')) {
                Log::info("Cambio de contraseña solicitado");
                if (!Hash::check($request->current_password, $user->password)) {
                    Log::error("Contraseña actual incorrecta");
                    return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])->withInput();
                }
                $user->password = Hash::make($request->new_password);
                Log::info("Contraseña actualizada");
            }
            
            try {
                $user->save();
                Log::info("Usuario guardado exitosamente");
            } catch (\Exception $e) {
                Log::error("Error al guardar usuario: " . $e->getMessage());
                throw $e;
            }
            
            // Actualizar o crear registro en la tabla de personas si es necesario
            if ($user->person_id) {
                $persona = \Modules\SICA\Entities\Person::find($user->person_id);
                if ($persona) {
                    // Actualizar teléfonos en la tabla people
                    $persona->telephone1 = $request->phone;
                    $persona->save();
                    Log::info("Persona actualizada para usuario {$user->id}");
                }
            }
            
            // Actualizar o crear pre-registro con la organización
            $preregistro = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($preregistro) {
                $preregistro->organization = $request->organization;
                $preregistro->phone = $request->phone;
                $preregistro->save();
                Log::info("Pre-registro actualizado para usuario {$user->id}");
            } else {
                // Crear nuevo pre-registro si no existe
                $preregistro = new \Modules\FABRICASOFT\Entities\Preregistration();
                $preregistro->email = $user->email;
                $preregistro->phone = $request->phone;
                $preregistro->organization = $request->organization;
                $preregistro->client_type = 'cliente_externo';
                $preregistro->status = 'pending';
                $preregistro->save();
                Log::info("Nuevo pre-registro creado para usuario {$user->id}");
            }
            
            DB::commit();
            Log::info("Transacción completada exitosamente");
            
            return redirect()->route('cliente_externo.ajustes')
                ->with('success', 'Tu perfil ha sido actualizado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error al actualizar perfil del usuario {$user->id}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return back()->withErrors(['error' => 'Ha ocurrido un error al actualizar tu perfil. Por favor, intenta nuevamente.'])->withInput();
        }
    }

    /**
     * Mostrar lista de proyectos para cliente interno
     */
    public function clienteInternoProjects()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_interno')) {
            abort(403, 'Acceso denegado. Solo clientes internos pueden acceder a esta sección.');
        }

        $user = Auth::user();
        
        // Primero verificar si el usuario tiene un preregistration asociado
        $preregistration = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)->first();
        
        // Obtener proyectos del cliente interno
        $proyectos = collect();
        if ($preregistration) {
            $proyectos = \Modules\FABRICASOFT\Entities\Project::where('preregistration_id', $preregistration->id)
                ->with(['preregistration', 'phases', 'teamMembers', 'scrumMaster'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('fabricasoft::cliente_interno.projects', compact('proyectos', 'preregistration'));
    }

    /**
     * Mostrar detalle de un proyecto específico para cliente interno
     */
    public function clienteInternoShowProject($id)
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_interno')) {
            abort(403, 'Acceso denegado. Solo clientes internos pueden acceder a esta sección.');
        }

        $user = Auth::user();
        
        // Verificar que el proyecto pertenezca al cliente interno
        $proyecto = \Modules\FABRICASOFT\Entities\Project::whereHas('preregistration', function($query) use ($user) {
            $query->where('email', $user->email);
        })->with(['preregistration', 'phases', 'teamMembers', 'scrumMaster', 'developers', 'testers', 'designers'])
          ->findOrFail($id);

        return view('fabricasoft::cliente_interno.show_project', compact('proyecto'));
    }

    /**
     * Mostrar formulario de nueva solicitud para cliente interno
     */
    public function clienteInternoNuevaSolicitudForm()
    {
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_interno')) {
            abort(403, 'Acceso denegado. Solo clientes internos pueden acceder a esta sección.');
        }

        $user = Auth::user();
        
        // Verificar si ya tiene un proyecto activo
        $proyectoActivo = \Modules\FABRICASOFT\Entities\Project::whereHas('preregistration', function($query) use ($user) {
            $query->where('email', $user->email);
        })->whereIn('status', ['planning', 'active'])->first();
        
        // Obtener información de la persona si existe
        $persona = null;
        if ($user->person_id) {
            $persona = \Modules\SICA\Entities\Person::find($user->person_id);
        }
        
        // Obtener preregistro existente si existe
        $preregistroExistente = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('fabricasoft::cliente_interno.nueva_solicitud', compact('user', 'persona', 'preregistroExistente', 'proyectoActivo'));
    }

    /**
     * Guardar nueva solicitud para cliente interno
     */
    public function clienteInternoGuardarNuevaSolicitud(Request $request)
    {
        \Log::info('=== MÉTODO clienteInternoGuardarNuevaSolicitud LLAMADO ===');
        \Log::info('Datos recibidos: ' . json_encode($request->all()));
        
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_interno')) {
            abort(403, 'Acceso denegado. Solo clientes internos pueden acceder a esta sección.');
        }

        $user = Auth::user();
        
        // Validar los datos del formulario
        $request->validate([
            'software_type' => 'required|string|in:aplicacion_web,aplicacion_movil,sistema_desktop,base_datos,api,otro',
            'project_description' => 'required|string|min:50',
            'additional_requirements' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'terms_accepted' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();
            
            // Crear nuevo preregistro
            $preregistro = new \Modules\FABRICASOFT\Entities\Preregistration();
            $preregistro->email = $user->email;
            $preregistro->full_name = $user->nickname ?: $user->name;
            $preregistro->phone = $request->phone ?: $user->phone;
            $preregistro->organization = $request->organization ?: 'SENA';
            $preregistro->software_type = $request->software_type;
            $preregistro->project_description = $request->project_description;
            $preregistro->additional_requirements = $request->additional_requirements;
            $preregistro->client_type = 'cliente_interno';
            $preregistro->status = 'pending';
            
            \Log::info('Guardando preregistro con datos:', [
                'email' => $preregistro->email,
                'full_name' => $preregistro->full_name,
                'software_type' => $preregistro->software_type,
                'client_type' => $preregistro->client_type,
                'status' => $preregistro->status,
                'phone' => $preregistro->phone,
                'organization' => $preregistro->organization
            ]);
            
            $preregistro->save();
            
            \Log::info('Preregistro guardado exitosamente con ID: ' . $preregistro->id);
            
            DB::commit();
            
            return redirect()->route('fabricasoft.cliente_interno.dashboard')
                ->with('success', 'Tu solicitud ha sido enviada exitosamente. Será revisada por nuestro equipo y te notificaremos el resultado.');
                
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Error al guardar nueva solicitud del cliente interno {$user->id}: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            return back()->withErrors(['error' => 'Ha ocurrido un error al enviar tu solicitud. Por favor, intenta nuevamente.'])->withInput();
        }
    }

    /**
     * Mostrar ajustes del perfil para cliente interno
     */
    public function clienteInternoAjustes()
    {
        \Log::info("=== MÉTODO clienteInternoAjustes LLAMADO ===");
        
        if (!Auth::user()->hasCustomRole('fabricasoft.cliente_interno')) {
            \Log::error("Usuario no tiene rol de cliente interno");
            abort(403, 'Acceso denegado. Solo clientes internos pueden acceder a esta sección.');
        }

        $user = Auth::user();
        \Log::info("Usuario autenticado correctamente - ID: " . $user->id . ", Email: " . $user->email);
        
        // Obtener datos del perfil
        $datosPerfil = [
            'name' => $user->nickname ?: $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'organization' => null
        ];
        
        \Log::info("Datos del perfil: " . json_encode($datosPerfil));
        
        // Obtener organización del preregistro si existe
        $preregistro = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($preregistro) {
            \Log::info("Preregistro encontrado ID: " . $preregistro->id);
            $datosPerfil['organization'] = $preregistro->organization;
            $datosPerfil['phone'] = $preregistro->phone ?: $datosPerfil['phone'];
            \Log::info("Datos del preregistro: organization=" . ($preregistro->organization ?? 'null') . ", phone=" . ($preregistro->phone ?? 'null'));
        } else {
            \Log::info("No se encontró preregistro para el usuario, creando uno inicial");
            
            // Crear un preregistro inicial básico para el usuario
            try {
                $preregistro = new \Modules\FABRICASOFT\Entities\Preregistration();
                $preregistro->email = $user->email;
                $preregistro->full_name = $user->nickname ?: $user->name ?: 'Usuario SENA';
                $preregistro->phone = $user->phone;
                $preregistro->organization = 'SENA';
                $preregistro->software_type = 'otro';
                $preregistro->project_description = 'Perfil de usuario interno del SENA';
                $preregistro->client_type = 'cliente_interno';
                $preregistro->status = 'pending';
                $preregistro->save();
                
                \Log::info("Preregistro inicial creado ID: " . $preregistro->id);
                
                // Actualizar los datos del perfil con la información del preregistro
                $datosPerfil['organization'] = $preregistro->organization;
                $datosPerfil['phone'] = $preregistro->phone;
                
            } catch (\Exception $e) {
                \Log::error("Error al crear preregistro inicial: " . $e->getMessage());
                // Continuar sin preregistro si hay error
            }
        }
        
        \Log::info("Datos finales del perfil: " . json_encode($datosPerfil));
        \Log::info("Retornando vista de ajustes");
        
        // Log adicional para verificar que los datos se pasan correctamente
        \Log::info("=== VERIFICACIÓN FINAL DE DATOS ===");
        \Log::info("name: " . ($datosPerfil['name'] ?? 'NULL'));
        \Log::info("email: " . ($datosPerfil['email'] ?? 'NULL'));
        \Log::info("phone: " . ($datosPerfil['phone'] ?? 'NULL'));
        \Log::info("organization: " . ($datosPerfil['organization'] ?? 'NULL'));
        
        // Verificar que el usuario tenga los campos necesarios
        \Log::info("=== VERIFICACIÓN DEL USUARIO ===");
        \Log::info("user->name: " . ($user->name ?? 'NULL'));
        \Log::info("user->nickname: " . ($user->nickname ?? 'NULL'));
        \Log::info("user->phone: " . ($user->phone ?? 'NULL'));
        \Log::info("user->email: " . ($user->email ?? 'NULL'));

        return view('fabricasoft::cliente_interno.ajustes', compact('datosPerfil'));
    }

    /**
     * Actualizar ajustes del perfil para cliente interno
     */
    public function clienteInternoActualizarAjustes(Request $request)
    {
        \Log::info('=== MÉTODO clienteInternoActualizarAjustes LLAMADO ===');
        \Log::info('Datos recibidos: ' . json_encode($request->all()));
        \Log::info('Usuario autenticado: ' . (Auth::check() ? 'SÍ' : 'NO'));
        \Log::info('Usuario ID: ' . (Auth::id() ?? 'NULL'));
        
        try {
            // Verificar si el usuario tiene el rol correcto
            if (!Auth::user()->hasCustomRole('fabricasoft.cliente_interno')) {
                abort(403, 'Acceso denegado. Solo clientes internos pueden acceder a esta sección.');
            }
            
            $user = Auth::user();
            
            // Verificar si es cambio de contraseña o actualización de perfil
            $isPasswordUpdate = $request->has('update_password') && $request->update_password == '1';
            
            if ($isPasswordUpdate) {
                // Validar solo campos de contraseña
                $request->validate([
                    'current_password' => 'required|string',
                    'new_password' => 'required|string|min:8|confirmed',
                ], [
                    'current_password.required' => 'La contraseña actual es obligatoria.',
                    'new_password.required' => 'La nueva contraseña es obligatoria.',
                    'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
                    'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
                ]);
                
                // Verificar contraseña actual
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])->withInput();
                }
                
                // Actualizar contraseña
                $user->password = Hash::make($request->new_password);
                $user->save();
                
                return redirect()->route('fabricasoft.cliente_interno.ajustes')
                    ->with('success', 'Tu contraseña ha sido cambiada exitosamente.');
                    
            } else {
                // Validar campos del perfil
                $request->validate([
                    'name' => 'required|string|max:255',
                    'phone' => 'nullable|string|max:20',
                    'organization' => 'nullable|string|max:255',
                ], [
                    'name.required' => 'El nombre es obligatorio.',
                    'name.max' => 'El nombre no puede tener más de 255 caracteres.',
                    'phone.max' => 'El teléfono no puede tener más de 20 caracteres.',
                    'organization.max' => 'La empresa/institución no puede tener más de 255 caracteres.',
                ]);
                
                DB::beginTransaction();
                
                try {
                    // Actualizar nombre del usuario
                    $user->nickname = $request->name;
                    
                    // Actualizar teléfono del usuario
                    $user->phone = $request->phone;
                    
                    $user->save();
                    
                    // Actualizar o crear preregistro con la organización
                    $preregistro = \Modules\FABRICASOFT\Entities\Preregistration::where('email', $user->email)
                        ->orderBy('created_at', 'desc')
                        ->first();
                        
                    if ($preregistro) {
                        $preregistro->organization = $request->organization;
                        $preregistro->phone = $request->phone;
                        $preregistro->full_name = $request->name; // Actualizar también el nombre
                        $preregistro->save();
                    } else {
                        // Crear nuevo preregistro si no existe
                        $preregistro = new \Modules\FABRICASOFT\Entities\Preregistration();
                        $preregistro->email = $user->email;
                        $preregistro->full_name = $request->name; // Agregar el nombre completo
                        $preregistro->phone = $request->phone;
                        $preregistro->organization = $request->organization;
                        $preregistro->client_type = 'cliente_interno';
                        $preregistro->status = 'pending';
                        $preregistro->save();
                    }
                    
                    DB::commit();
                    
                    return redirect()->route('fabricasoft.cliente_interno.ajustes')
                        ->with('success', 'Tu perfil ha sido actualizado exitosamente.');
                        
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }
            
        } catch (\Exception $e) {
            \Log::error("Error al actualizar perfil del cliente interno: " . $e->getMessage());
            return back()->withErrors(['error' => 'Ha ocurrido un error al actualizar tu perfil. Por favor, intenta nuevamente.'])->withInput();
        }
    }

    /**
     * Mostrar ajustes del perfil para desarrollador
     */
    public function desarrolladorAjustes()
    {
        \Log::info("=== MÉTODO desarrolladorAjustes LLAMADO ===");
        
        if (!Auth::user()->hasCustomRole('fabricasoft.desarrollador')) {
            \Log::error("Usuario no tiene rol de desarrollador");
            abort(403, 'Acceso denegado. Solo desarrolladores pueden acceder a esta sección.');
        }

        $user = Auth::user();
        \Log::info("Usuario autenticado correctamente - ID: " . $user->id . ", Email: " . $user->email);
        
        // Obtener datos del perfil
        $datosPerfil = [
            'name' => $user->nickname ?: $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'organization' => 'SENA'
        ];
        
        \Log::info("Datos del perfil: " . json_encode($datosPerfil));
        
        return view('fabricasoft::desarrollador.ajustes', compact('datosPerfil'));
    }

    /**
     * Actualizar ajustes del perfil para desarrollador
     */
    public function desarrolladorActualizarAjustes(Request $request)
    {
        \Log::info('=== MÉTODO desarrolladorActualizarAjustes LLAMADO ===');
        \Log::info('Datos recibidos: ' . json_encode($request->all()));
        
        try {
            // Verificar si el usuario tiene el rol correcto
            if (!Auth::user()->hasCustomRole('fabricasoft.desarrollador')) {
                abort(403, 'Acceso denegado. Solo desarrolladores pueden acceder a esta sección.');
            }

            $user = Auth::user();
            
            // Validar los datos del formulario
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
            ]);

            // Actualizar el usuario
            $user->nickname = $user->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            \Log::info('Perfil del desarrollador actualizado exitosamente - ID: ' . $user->id);

            return redirect()->route('fabricasoft.desarrollador.ajustes')
                ->with('success', 'Perfil actualizado exitosamente.');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar perfil del desarrollador: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Ha ocurrido un error al actualizar tu perfil. Por favor, intenta nuevamente.'])->withInput();
        }
    }
}