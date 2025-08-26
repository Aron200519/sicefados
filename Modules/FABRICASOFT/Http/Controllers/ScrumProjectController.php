<?php

namespace Modules\FABRICASOFT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\FABRICASOFT\Entities\Project;
use Modules\FABRICASOFT\Entities\ProjectTeamMember;
use Modules\FABRICASOFT\Entities\ProjectPhase;
use Modules\FABRICASOFT\Entities\Preregistration;
use Modules\FABRICASOFT\Entities\PhaseInfoHistory;

class ScrumProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Crear un nuevo equipo Scrum desde una solicitud aprobada
     */
    public function createProject(Request $request, $preregistrationId)
    {
        $preregistration = Preregistration::findOrFail($preregistrationId);
        
        // Log para debugging
        Log::info('Intentando crear proyecto Scrum', [
            'preregistration_id' => $preregistrationId,
            'status' => $preregistration->status,
            'workflow_status' => $preregistration->workflow_status ?? 'no existe',
            'request_data' => $request->all()
        ]);
        
        // Verificar que la solicitud esté aprobada
        if ($preregistration->status !== 'approved') {
            Log::warning('Solicitud no aprobada para crear proyecto', [
                'preregistration_id' => $preregistrationId,
                'status' => $preregistration->status,
                'expected' => 'approved'
            ]);
            return back()->withErrors(['error' => 'Solo se pueden crear proyectos para solicitudes aprobadas.']);
        }

        // Verificar que no exista ya un proyecto para esta solicitud
        if (Project::where('preregistration_id', $preregistrationId)->exists()) {
            return back()->withErrors(['error' => 'Ya existe un proyecto para esta solicitud.']);
        }

        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'scrum_master_id' => 'required|exists:users,id',
            'start_date' => 'nullable|date|after_or_equal:today',
            'estimated_end_date' => 'nullable|date|after:start_date',
            'project_goals' => 'nullable|string|max:1000',
            'success_criteria' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            Log::info('Creando proyecto en base de datos', [
                'preregistration_id' => $preregistrationId,
                'project_name' => $request->project_name,
                'scrum_master_id' => $request->scrum_master_id
            ]);

            // Crear el proyecto
            $project = Project::create([
                'preregistration_id' => $preregistrationId,
                'project_name' => $request->project_name,
                'description' => $request->description,
                'status' => 'planning',
                'scrum_master_id' => $request->scrum_master_id,
                'start_date' => $request->start_date,
                'estimated_end_date' => $request->estimated_end_date,
                'project_goals' => $request->project_goals,
                'success_criteria' => $request->success_criteria,
            ]);

            // Crear fases iniciales del proyecto
            Log::info('Creando fases iniciales del proyecto', [
                'project_id' => $project->id
            ]);
            
            $this->createInitialPhases($project);

            DB::commit();

            Log::info('Proyecto Scrum creado exitosamente', [
                'project_id' => $project->id,
                'project_name' => $project->project_name,
                'scrum_master_id' => $project->scrum_master_id,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('fabricasoft.admin.projects.show', $project->id)
                ->with('success', 'Equipo Scrum creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando equipo Scrum', [
                'preregistration_id' => $preregistrationId,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error al crear el proyecto. Por favor, inténtalo de nuevo.']);
        }
    }

    /**
     * Crear fases iniciales del proyecto
     */
    private function createInitialPhases(Project $project)
    {
        $phases = [
            [
                'phase_name' => 'Especificaciones del documento del software',
                'description' => 'Recepción y análisis inicial de la solicitud del cliente. El admin revisa y aprueba la solicitud.',
                'type' => 'milestone',
                'status' => 'completed', // Se completa cuando se crea el proyecto
                'duration_days' => 3,
                'order' => 1,
            ],
            [
                'phase_name' => 'Análisis y SRS',
                'description' => 'El analista revisa la solicitud, se comunica con el cliente y crea el documento de Especificación de Requisitos del Software (SRS).',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 10,
                'order' => 2,
            ],
            [
                'phase_name' => 'Diseño del Sistema',
                'description' => 'Diseño de la arquitectura, base de datos, interfaces y componentes del sistema basado en el SRS.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 14,
                'order' => 3,
            ],
            [
                'phase_name' => 'Codificación',
                'description' => 'Desarrollo e implementación del código según el diseño establecido. Los desarrolladores trabajan en las funcionalidades.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 21,
                'order' => 4,
            ],
            [
                'phase_name' => 'Pruebas',
                'description' => 'Ejecución de pruebas unitarias, de integración y del sistema para validar la funcionalidad.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 10,
                'order' => 5,
            ],
            [
                'phase_name' => 'Implementación',
                'description' => 'Implementación del sistema en el entorno de producción, configuración de servidores y despliegue final.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 7,
                'order' => 6,
            ],
            [
                'phase_name' => 'Mantenimiento',
                'description' => 'Soporte post-entrega, corrección de errores y mejoras continuas del sistema.',
                'type' => 'milestone',
                'status' => 'planned',
                'duration_days' => 30,
                'order' => 7,
            ],
        ];

        foreach ($phases as $phaseData) {
            ProjectPhase::create(array_merge($phaseData, ['project_id' => $project->id]));
        }
    }

    /**
     * Agregar desarrollador al equipo del proyecto
     */
    public function addTeamMember(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden agregar miembros al equipo.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:developer,tester,designer,business_analyst',
            'responsibilities' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verificar si el usuario ya está en el equipo (activo o removido)
        $existingMember = ProjectTeamMember::where('project_id', $projectId)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existingMember) {
            if ($existingMember->status === 'active') {
                return back()->withErrors(['error' => 'El usuario ya es miembro activo del equipo.']);
            }
            
            // Si el usuario fue removido anteriormente, reactivarlo
            try {
                $existingMember->update([
                    'role' => $request->role,
                    'status' => 'active',
                    'joined_date' => now(),
                    'left_date' => null,
                    'responsibilities' => $request->responsibilities,
                    'notes' => $request->notes,
                ]);

                Log::info('Miembro reactivado en el equipo Scrum', [
                    'project_id' => $projectId,
                    'user_id' => $request->user_id,
                    'role' => $request->role,
                    'reactivated_by' => Auth::id(),
                ]);

                return back()->with('success', 'Miembro reactivado en el equipo exitosamente.');
            } catch (\Exception $e) {
                Log::error('Error reactivando miembro del equipo', [
                    'project_id' => $projectId,
                    'user_id' => $request->user_id,
                    'error' => $e->getMessage(),
                ]);

                return back()->withErrors(['error' => 'Error al reactivar miembro del equipo.']);
            }
        }

        // Si el usuario no existe, crear nuevo miembro
        try {
            ProjectTeamMember::create([
                'project_id' => $projectId,
                'user_id' => $request->user_id,
                'role' => $request->role,
                'status' => 'active',
                'joined_date' => now(),
                'responsibilities' => $request->responsibilities,
                'notes' => $request->notes,
            ]);

            Log::info('Miembro agregado al equipo Scrum', [
                'project_id' => $projectId,
                'user_id' => $request->user_id,
                'role' => $request->role,
                'added_by' => Auth::id(),
            ]);

            return back()->with('success', 'Miembro agregado al equipo exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error agregando miembro al equipo', [
                'project_id' => $projectId,
                'user_id' => $request->user_id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error al agregar miembro al equipo.']);
        }
    }

    /**
     * Remover miembro del equipo
     */
    public function removeTeamMember($projectId, $memberId)
    {
        $project = Project::findOrFail($projectId);
        $member = ProjectTeamMember::findOrFail($memberId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            return response()->json(['success' => false, 'message' => 'Solo el Scrum Master o un Admin pueden remover miembros del equipo.'], 403);
        }

        try {
            $member->update([
                'status' => 'removed',
                'left_date' => now(),
            ]);

            Log::info('Miembro removido del equipo Scrum', [
                'project_id' => $projectId,
                'member_id' => $memberId,
                'removed_by' => Auth::id(),
            ]);

            return response()->json(['success' => true, 'message' => 'Miembro removido del equipo exitosamente.']);

        } catch (\Exception $e) {
            Log::error('Error removiendo miembro del equipo', [
                'project_id' => $projectId,
                'member_id' => $memberId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Error al remover miembro del equipo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar el equipo Scrum
     */
    public function showProject($projectId)
    {
        $project = Project::with([
            'preregistration',
            'request',
            'scrumMaster',
            'teamMembers.user',
            'phases'
        ])->findOrFail($projectId);

        // Obtener usuarios disponibles para agregar al equipo
        $availableUsers = \App\Models\User::whereHas('roles', function($query) {
            $query->where('slug', 'fabricasoft.desarrollador'); // SOLO desarrolladores
        })->whereNotIn('id', function($subquery) use ($projectId) {
            // Solo excluir usuarios ya asignados a ESTE proyecto específico (no removidos)
            $subquery->select('user_id')
                ->from('fabricasoft_project_team_members')
                ->where('project_id', $projectId)
                ->where('status', '!=', 'removed');
        })->get();

        // Log detallado de la consulta
        Log::info('Consulta de usuarios disponibles', [
            'query' => 'whereHas roles with slug in [fabricasoft.desarrollador, fabricasoft.analista]',
            'result_count' => $availableUsers->count(),
            'users' => $availableUsers->map(function($user) {
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'nickname' => $user->nickname,
                    'roles' => $user->roles->pluck('slug')->toArray()
                ];
            })->toArray()
        ]);

        // Log detallado para debugging
        Log::info('Desarrolladores disponibles para el proyecto', [
            'project_id' => $projectId,
            'total_desarrolladores' => $availableUsers->count(),
            'desarrolladores' => $availableUsers->pluck('id', 'email')->toArray(),
            'total_desarrolladores_sistema' => \App\Models\User::whereHas('roles', function($query) {
                $query->where('slug', 'fabricasoft.desarrollador');
            })->count(),
            'desarrolladores_sistema' => \App\Models\User::whereHas('roles', function($query) {
                $query->where('slug', 'fabricasoft.desarrollador');
            })->pluck('id', 'email')->toArray(),
            'team_members_proyecto' => $project->teamMembers->pluck('user_id', 'status')->toArray()
        ]);

        return view('fabricasoft::admin.scrum_project.show', compact('project', 'availableUsers'));
    }

    /**
     * Listar todos los equipos Scrum
     */
    public function listProjects()
    {
        $projects = Project::with([
            'preregistration',
            'scrumMaster',
            'teamMembers'
        ])->orderBy('created_at', 'desc')->paginate(15);

        return view('fabricasoft::admin.scrum_project.list', compact('projects'));
    }

    /**
     * Asignar usuario a una fase
     */
    public function assignPhase(Request $request, $projectId, $phaseId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden asignar fases.');
        }

        $phase = ProjectPhase::where('project_id', $projectId)
            ->where('id', $phaseId)
            ->firstOrFail();

        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        try {
            $phase->update([
                'assigned_to' => $request->user_id ?: null,
            ]);

            Log::info('Fase asignada exitosamente', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'assigned_to' => $request->user_id,
                'assigned_by' => Auth::id(),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error asignando fase', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al asignar la fase.'], 500);
        }
    }

    /**
     * Iniciar una fase
     */
    public function startPhase(Request $request, $projectId, $phaseId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden iniciar fases.');
        }

        $phase = ProjectPhase::where('project_id', $projectId)
            ->where('id', $phaseId)
            ->firstOrFail();

        try {
            $phase->update([
                'status' => 'in_progress',
                'start_date' => now(),
            ]);

            Log::info('Fase iniciada exitosamente', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'started_by' => Auth::id(),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error iniciando fase', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al iniciar la fase.'], 500);
        }
    }

    /**
     * Completar una fase
     */
    public function completePhase(Request $request, $projectId, $phaseId)
    {
        // Log de depuración
        Log::info('completePhase llamado', [
            'project_id' => $projectId,
            'phase_id' => $phaseId,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            Log::warning('Usuario no autorizado para completar fase', [
                'user_id' => Auth::id(),
                'scrum_master_id' => $project->scrum_master_id,
                'has_admin_role' => Auth::user()->hasCustomRole('fabricasoft.admin')
            ]);
            abort(403, 'Solo el Scrum Master o un Admin pueden completar fases.');
        }

        $phase = ProjectPhase::where('project_id', $projectId)
            ->where('id', $phaseId)
            ->firstOrFail();

        Log::info('Fase encontrada', [
            'phase_status' => $phase->status,
            'phase_name' => $phase->phase_name
        ]);

        try {
            $phase->update([
                'status' => 'completed',
                'end_date' => now(),
            ]);

            // Actualizar el estado del proyecto según el progreso de las fases
            $this->updateProjectStatus($projectId);

            Log::info('Fase completada exitosamente', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'completed_by' => Auth::id(),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error completando fase', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Error al completar la fase.'], 500);
        }
    }



    /**
     * Completar fase con información adicional
     */
    public function completePhaseWithInfo(Request $request, $projectId, $phaseId)
    {
        // Log de depuración
        Log::info('completePhaseWithInfo llamado', [
            'project_id' => $projectId,
            'phase_id' => $phaseId,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            Log::warning('Usuario no autorizado para completar fase con info', [
                'user_id' => Auth::id(),
                'scrum_master_id' => $project->scrum_master_id,
                'has_admin_role' => Auth::user()->hasCustomRole('fabricasoft.admin')
            ]);
            abort(403, 'Solo el Scrum Master o un Admin pueden completar fases.');
        }

        $phase = ProjectPhase::where('project_id', $projectId)
            ->where('id', $phaseId)
            ->firstOrFail();

        Log::info('Fase encontrada para completar con info', [
            'phase_status' => $phase->status,
            'phase_name' => $phase->phase_name
        ]);

        // Validar la información según el tipo de fase
        $phaseOrder = $request->input('phase_order');
        
        if ($phaseOrder == 4) {
            // Validación para Fase 4 (Codificación)
            $request->validate([
                'desarrollador' => 'required|exists:users,id',
                'modulo' => 'required|string|max:200',
                'fecha_desarrollo' => 'required|date',
                'nombre_commit' => 'required|string|max:200',
                'url_repositorio' => 'required|url|max:500',
                'rama' => 'nullable|string|max:100',
            ]);
        } elseif ($phaseOrder == 6) {
            // Validación para Fase 6 (Implementación)
            $request->validate([
                'descripcion' => 'required|string|max:1000',
                'usuario' => 'required|string|max:200',
                'contrasena' => 'required|string|max:200',
                'dominio' => 'required|string|max:200',
                'url_completo' => 'required|url|max:500',
                'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            ]);
        } else {
            // Validación para otras fases (campos generales)
            $request->validate([
                'descripcion' => 'required|string|max:1000',
                'notas' => 'nullable|string|max:500',
                'enlace' => 'nullable|url|max:500',
                'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
                'comentario_adicional' => 'nullable|string|max:500',
                'fecha_creacion' => 'nullable|date',
            ]);
        }

        try {
            // Procesar documento si se subió
            $documentPath = null;
            if ($request->hasFile('documento')) {
                $document = $request->file('documento');
                $documentName = time() . '_' . $document->getClientOriginalName();
                $documentPath = $document->storeAs('fabricasoft/documents', $documentName, 'public');
            }

            // Crear registro en PhaseInfoHistory con la información de cierre
            if ($phaseOrder == 4) {
                // Para Fase 4, usar los campos específicos de desarrollo
                PhaseInfoHistory::create([
                    'project_id' => $projectId,
                    'phase_id' => $phaseId,
                    'info_type' => 'complete_info',
                    'content' => 'Información de cierre de fase de codificación',
                    'description' => "Módulo: " . $request->modulo . " | Commit: " . $request->nombre_commit,
                    'notes' => "Desarrollador: " . $request->desarrollador . " | Rama: " . ($request->rama ?: 'No especificada'),
                    'external_link' => $request->url_repositorio,
                    'document_path' => $documentPath,
                    'additional_comment' => "Fecha de desarrollo: " . $request->fecha_desarrollo,
                    'start_date' => $request->fecha_desarrollo,
                    'end_date' => $request->fecha_desarrollo,
                    'added_by' => Auth::id(),
                ]);
            } else {
                // Para otras fases, usar los campos generales
                PhaseInfoHistory::create([
                    'project_id' => $projectId,
                    'phase_id' => $phaseId,
                    'info_type' => 'complete_info',
                    'content' => 'Información de cierre de fase',
                    'description' => $request->descripcion,
                    'notes' => $request->notas,
                    'external_link' => $request->enlace,
                    'document_path' => $documentPath,
                    'additional_comment' => $request->comentario_adicional,
                    'start_date' => $request->fecha_creacion ? $request->fecha_creacion : now()->toDateString(),
                    'end_date' => $request->fecha_creacion ? $request->fecha_creacion : now()->toDateString(),
                    'added_by' => Auth::id(),
                ]);
            }

            // Marcar la fase como completada
            $phase->update([
                'status' => 'completed',
                'end_date' => now(),
            ]);

            // Actualizar el estado del proyecto según el progreso de las fases
            Log::info('Llamando a updateProjectStatus desde completePhaseWithInfo', [
                'project_id' => $projectId,
                'phase_id' => $phaseId
            ]);
            
            try {
                $this->updateProjectStatus($projectId);
                Log::info('updateProjectStatus ejecutado exitosamente');
            } catch (\Exception $e) {
                Log::error('Error ejecutando updateProjectStatus', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            Log::info('Fase completada exitosamente con información', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'completed_by' => Auth::id(),
                'has_document' => !is_null($documentPath),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error completando fase con información', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Error al completar la fase con información.'], 500);
        }
    }

    /**
     * Guardar información de una fase
     */
    public function savePhase(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden guardar fases.');
        }

        $request->validate([
            'phase_id' => 'required|exists:fabricasoft_project_phases,id',
            'phase_title' => 'required|string|max:255',
            'phase_description' => 'required|string|max:1000',
            'phase_status' => 'required|in:in_progress,completed',
            'phase_notes' => 'nullable|string|max:500',
            'phase_link' => 'nullable|url|max:500',
            'phase_document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        try {
            $phase = ProjectPhase::where('project_id', $projectId)
                ->where('id', $request->phase_id)
                ->firstOrFail();

            // Log temporal para depuración
            Log::info('Datos recibidos en savePhase', [
                'phase_notes' => $request->phase_notes,
                'phase_link' => $request->phase_link,
                'has_document' => $request->hasFile('phase_document'),
                'all_request_data' => $request->all()
            ]);

            $updateData = [
                'phase_name' => $request->phase_title,
                'description' => $request->phase_description,
                'status' => $request->phase_status,
                'notes' => $request->phase_notes,
                'external_link' => $request->phase_link,
            ];

            // Si se subió un documento
            if ($request->hasFile('phase_document')) {
                $document = $request->file('phase_document');
                $documentName = 'phase_' . $phase->id . '_' . time() . '.' . $document->getClientOriginalExtension();
                $documentPath = $document->storeAs('project_phases', $documentName, 'public');
                $updateData['document_path'] = $documentPath;
            }

            // Si se marcó como en progreso, agregar fecha de inicio
            if ($request->phase_status === 'in_progress' && !$phase->start_date) {
                $updateData['start_date'] = now();
            }
            
            // Si se marcó como completada, agregar fecha de finalización y asegurar fecha de inicio
            if ($request->phase_status === 'completed') {
                $updateData['end_date'] = now();
                // Si no tiene fecha de inicio, establecerla ahora
                if (!$phase->start_date) {
                    $updateData['start_date'] = now();
                }
            }

            $phase->update($updateData);

            Log::info('Fase guardada exitosamente', [
                'project_id' => $projectId,
                'phase_id' => $phase->id,
                'status' => $request->phase_status,
                'saved_by' => Auth::id(),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error guardando fase', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al guardar la fase.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Guardar información de la Fase 2: Análisis y SRS
     */
    public function savePhase2(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            
            // Verificar permisos
            if (!auth()->user()->hasCustomRole('fabricasoft.admin') && 
                !auth()->user()->hasCustomRole('fabricasoft.analista') &&
                auth()->id() != $project->scrum_master_id) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'], 403);
            }
            
            // Validar datos
            $request->validate([
                'descripcion' => 'required|string|max:1000',
                'notas' => 'nullable|string|max:500',
                'enlace' => 'nullable|url|max:255',
                'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
                'inicio' => 'required|date',
                'fin' => 'required|date|after:inicio',
            ]);
            
            // Buscar la fase 2 (Análisis y SRS)
            $phase2 = $project->phases()->where('phase_name', 'Análisis y SRS')->first();
            
            if (!$phase2) {
                return response()->json(['success' => false, 'message' => 'Fase 2 no encontrada.'], 404);
            }
            
            // Procesar documento si se subió
            $documentPath = null;
            if ($request->hasFile('documento')) {
                $file = $request->file('documento');
                $fileName = 'fase2_srs_' . time() . '_' . $file->getClientOriginalName();
                $documentPath = $file->storeAs('fabricasoft/proyectos/' . $project->id . '/fase2', $fileName, 'public');
            }
            
            // Actualizar la fase 2
            $phase2->update([
                'description' => $request->descripcion,
                'notes' => $request->notas,
                'external_link' => $request->enlace,
                'document_path' => $documentPath,
                'start_date' => $request->inicio,
                'end_date' => $request->fin,
                'status' => 'completed',
            ]);
            
            // Crear registro en el historial
            PhaseInfoHistory::create([
                'project_id' => $project->id,
                'phase_id' => $phase2->id,
                'info_type' => 'description',
                'content' => $request->descripcion,
                'added_by' => auth()->id(),
            ]);
            
            if ($request->notas) {
                PhaseInfoHistory::create([
                    'project_id' => $project->id,
                    'phase_id' => $phase2->id,
                    'info_type' => 'notes',
                    'content' => $request->notas,
                    'added_by' => auth()->id(),
                ]);
            }
            
            if ($request->enlace) {
                PhaseInfoHistory::create([
                    'project_id' => $project->id,
                    'phase_id' => $phase2->id,
                    'info_type' => 'link',
                    'content' => $request->enlace,
                    'added_by' => auth()->id(),
                ]);
            }
            
            if ($documentPath) {
                PhaseInfoHistory::create([
                    'project_id' => $project->id,
                    'phase_id' => $phase2->id,
                    'info_type' => 'document',
                    'document_path' => $documentPath,
                    'added_by' => auth()->id(),
                ]);
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Fase 2 completada exitosamente.',
                'phase' => $phase2
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al guardar Fase 2: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar la fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Habilitar la siguiente fase
     */
    public function enableNextPhase(Request $request, $projectId, $phaseId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden habilitar fases.');
        }

        try {
            $currentPhase = ProjectPhase::where('project_id', $projectId)
                ->where('id', $phaseId)
                ->firstOrFail();

            // Verificar que la fase actual esté completada
            if ($currentPhase->status !== 'completed') {
                return response()->json(['error' => 'La fase actual debe estar completada para habilitar la siguiente.'], 400);
            }

            // Buscar la siguiente fase
            $nextPhase = ProjectPhase::where('project_id', $projectId)
                ->where('order', '>', $currentPhase->order)
                ->orderBy('order')
                ->first();

            if (!$nextPhase) {
                return response()->json(['error' => 'No hay más fases para habilitar.'], 400);
            }

            // Habilitar la siguiente fase
            $nextPhase->update([
                'status' => 'planned',
            ]);

            Log::info('Siguiente fase habilitada', [
                'project_id' => $projectId,
                'current_phase_id' => $phaseId,
                'next_phase_id' => $nextPhase->id,
                'enabled_by' => Auth::id(),
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error habilitando siguiente fase', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'error' => $e->getMessage(),
            ]);

                         return response()->json(['error' => 'Error al habilitar la siguiente fase.', 'message' => $e->getMessage()], 500);
         }
     }

         /**
     * Agregar nueva información a una fase
     */
    public function addPhaseInfo(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden agregar información a las fases.');
        }

        $request->validate([
            'phase_id' => 'required|exists:fabricasoft_project_phases,id',
            'phase_order' => 'required|integer',
            'nueva_descripcion' => 'nullable|string|max:1000',
            'nuevas_notas' => 'nullable|string|max:500',
            'nuevo_enlace' => 'nullable|url|max:500',
            'nuevo_documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'comentario_adicional' => 'nullable|string|max:500',
            'fecha_creacion' => 'nullable|date',
            // Campos específicos para Fase 4 (Codificación)
            'modulo' => 'nullable|string|max:255',
            'desarrollador' => 'nullable|string|max:255',
            'fecha_desarrollo' => 'nullable|date',
            'nombre_commit' => 'nullable|string|max:255',
            'url_repositorio' => 'nullable|url|max:500',
            'rama' => 'nullable|string|max:100',
            // Campos específicos para Fase 6 (Implementación)
            'descripcion' => 'nullable|string|max:1000',
            'usuario' => 'nullable|string|max:200',
            'contrasena' => 'nullable|string|max:200',
            'dominio' => 'nullable|string|max:200',
            'url_completo' => 'nullable|url|max:500',
        ]);
        
        // Log de validación
        Log::info('Validación completada para Fase 6', [
            'phase_order' => $request->phase_order,
            'descripcion_presente' => $request->has('descripcion'),
            'usuario_presente' => $request->has('usuario'),
            'contrasena_presente' => $request->has('contrasena'),
            'dominio_presente' => $request->has('dominio'),
            'url_completo_presente' => $request->has('url_completo'),
        ]);

        try {
            // Log de depuración de la request
            Log::info('Request recibida en addPhaseInfo', [
                'all_data' => $request->all(),
                'phase_id' => $request->phase_id,
                'phase_order' => $request->phase_order,
                'descripcion' => $request->descripcion,
                'usuario' => $request->usuario,
                'contrasena' => $request->contrasena,
                'dominio' => $request->dominio,
                'url_completo' => $request->url_completo,
            ]);
            
            $phase = ProjectPhase::where('project_id', $projectId)
                ->where('id', $request->phase_id)
                ->firstOrFail();

            // Log para depuración
            Log::info('Agregando nueva información a la fase', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'nueva_descripcion' => $request->nueva_descripcion,
                'nuevas_notas' => $request->nuevas_notas,
                'nuevo_enlace' => $request->nuevo_enlace,
                'has_documento' => $request->hasFile('nuevo_documento'),
                'comentario_adicional' => $request->comentario_adicional
            ]);

            $documentPath = null;
            
            // Procesar documento si se subió
            if ($request->hasFile('nuevo_documento')) {
                $document = $request->file('nuevo_documento');
                $documentName = 'phase_' . $phase->id . '_additional_' . time() . '.' . $document->getClientOriginalExtension();
                $documentPath = $document->storeAs('project_phases', $documentName, 'public');
            }

            // Crear nuevo registro en PhaseInfoHistory con la información completa
            $description = '';
            $notes = '';
            $externalLink = '';
            $additionalComment = '';
            
            // Procesar campos según el tipo de fase
            if ($request->phase_order == 4) {
                // Fase 4 (Codificación) - Campos específicos de desarrollo
                if ($request->modulo) {
                    $description = "Módulo: " . $request->modulo;
                    if ($request->nombre_commit) {
                        $description .= " | Commit: " . $request->nombre_commit;
                    }
                }
                
                if ($request->desarrollador) {
                    // Obtener el nombre del desarrollador
                    $desarrolladorName = 'Desarrollador';
                    try {
                        $user = \App\Models\User::find($request->desarrollador);
                        if ($user) {
                            $desarrolladorName = $user->nickname ?? $user->name ?? 'Desarrollador';
                        }
                    } catch (\Exception $e) {
                        $desarrolladorName = 'Desarrollador';
                    }
                    
                    $notes = "Desarrollador: " . $request->desarrollador . " | Nombre: " . $desarrolladorName;
                    if ($request->rama) {
                        $notes .= " | Rama: " . $request->rama;
                    }
                }
                
                if ($request->url_repositorio) {
                    $externalLink = $request->url_repositorio;
                }
                
                if ($request->fecha_desarrollo) {
                    $additionalComment = "Fecha de desarrollo: " . $request->fecha_desarrollo;
                }
            } elseif ($request->phase_order == 6) {
                // Fase 6 (Implementación) - Campos específicos de implementación
                Log::info('Procesando Fase 6 - Campos recibidos:', [
                    'descripcion' => $request->descripcion,
                    'usuario' => $request->usuario,
                    'contrasena' => $request->contrasena,
                    'dominio' => $request->dominio,
                    'url_completo' => $request->url_completo,
                ]);
                
                $description = $request->descripcion ?: '';
                
                if ($request->usuario || $request->contrasena) {
                    $notes = '';
                    if ($request->usuario) {
                        $notes .= "Usuario: " . $request->usuario;
                    }
                    if ($request->contrasena) {
                        if ($notes) $notes .= " | ";
                        $notes .= "Contraseña: " . $request->contrasena;
                    }
                }
                
                if ($request->dominio || $request->url_completo) {
                    $additionalComment = '';
                    if ($request->dominio) {
                        $additionalComment .= "Dominio: " . $request->dominio;
                    }
                    if ($request->url_completo) {
                        if ($additionalComment) $additionalComment .= " | ";
                        $additionalComment .= "URL Completo: " . $request->url_completo;
                    }
                }
                
                // El campo external_link se puede usar para la URL completa también
                if ($request->url_completo) {
                    $externalLink = $request->url_completo;
                }
                
                Log::info('Fase 6 - Datos procesados:', [
                    'description' => $description,
                    'notes' => $notes,
                    'external_link' => $externalLink,
                    'additional_comment' => $additionalComment,
                ]);
            } else {
                // Otras fases - Campos generales
                $description = $request->descripcion ?: '';
                $notes = $request->notas ?: '';
                $externalLink = $request->enlace ?: '';
                $additionalComment = $request->comentario_adicional ?: '';
            }
            
            // Log de depuración antes de crear el registro
            Log::info('Datos a guardar en PhaseInfoHistory', [
                'description' => $description,
                'notes' => $notes,
                'external_link' => $externalLink,
                'additional_comment' => $additionalComment,
            ]);
            
            PhaseInfoHistory::create([
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'info_type' => 'complete_info',
                'content' => 'Nueva información agregada',
                'description' => $description,
                'notes' => $notes,
                'external_link' => $externalLink,
                'document_path' => $documentPath,
                'additional_comment' => $additionalComment,
                'start_date' => $request->fecha_creacion ? $request->fecha_creacion : now()->toDateString(),
                'end_date' => $request->fecha_creacion ? $request->fecha_creacion : now()->toDateString(),
                'added_by' => Auth::id(),
            ]);

            Log::info('Nueva información agregada exitosamente a la fase', [
                'project_id' => $projectId,
                'phase_id' => $phase->id,
                'added_by' => Auth::id(),
            ]);

            // Actualizar el estado del proyecto según el progreso de las fases
            $this->updateProjectStatus($projectId);

            return response()->json([
                'success' => true,
                'message' => "Se agregó nueva información a la fase exitosamente."
            ]);

        } catch (\Exception $e) {
            Log::error('Error agregando nueva información a la fase', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al agregar información a la fase: ' . $e->getMessage()
            ], 500);
        }
    }

     /**
      * Actualizar información del historial de una fase
      */
     public function updatePhaseInfo(Request $request, $projectId)
     {
         $project = Project::findOrFail($projectId);
         
         // Verificar permisos
         if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
             abort(403, 'Solo el Scrum Master o un Admin pueden editar la información del historial.');
         }

         $request->validate([
             'history_id' => 'required|exists:fabricasoft_phase_info_history,id',
             'phase_order' => 'nullable|integer',
             'modulo' => 'nullable|string|max:255',
             'desarrollador' => 'nullable|integer|exists:users,id',
             'fecha_desarrollo' => 'nullable|date',
             'nombre_commit' => 'nullable|string|max:255',
             'url_repositorio' => 'nullable|url|max:500',
             'rama' => 'nullable|string|max:100',
             'descripcion' => 'nullable|string|max:1000',
             'notas' => 'nullable|string|max:1000',
             'enlace' => 'nullable|url|max:500',
             'fecha_creacion' => 'nullable|date',
             'comentario_adicional' => 'nullable|string|max:1000',
             'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
             // Campos específicos para Fase 6 (Implementación)
             'usuario' => 'nullable|string|max:200',
             'contrasena' => 'nullable|string|max:200',
             'dominio' => 'nullable|string|max:200',
             'url_completo' => 'nullable|url|max:500',
         ]);

         try {
             // Log de depuración de la request
             Log::info('Request recibida en updatePhaseInfo', [
                 'all_data' => $request->all(),
                 'history_id' => $request->history_id,
                 'phase_order' => $request->phase_order,
                 'method' => $request->method(),
                 'url' => $request->url(),
                 'headers' => $request->headers->all(),
             ]);
             
             $historyItem = PhaseInfoHistory::findOrFail($request->history_id);
             
             // Verificar que pertenezca al proyecto
             if ($historyItem->project_id != $projectId) {
                 return response()->json(['error' => 'El elemento del historial no pertenece a este proyecto.'], 400);
             }

             Log::info('Actualizando información del historial', [
                 'history_id' => $historyItem->id,
                 'project_id' => $projectId,
                 'phase_id' => $historyItem->phase_id,
                 'updated_by' => Auth::id(),
             ]);

                         // Procesar campos según el tipo de fase
            $description = '';
            $notes = '';
            $externalLink = '';
            $additionalComment = '';
            
            // Log de depuración
            Log::info('Procesando información de fase', [
                'phase_order' => $request->phase_order,
                'modulo' => $request->modulo,
                'desarrollador' => $request->desarrollador,
                'fecha_desarrollo' => $request->fecha_desarrollo,
                'nombre_commit' => $request->nombre_commit,
                'url_repositorio' => $request->url_repositorio,
                'rama' => $request->rama,
            ]);
            
            if ($request->phase_order == 4) {
                 // Fase 4 (Codificación) - Campos específicos de desarrollo
                 $description = '';
                 $notes = '';
                 $externalLink = '';
                 $additionalComment = '';
                 
                 // Procesar módulo y commit - siempre procesar, incluso si están vacíos
                 $modulo = $request->input('modulo', '');
                 $nombreCommit = $request->input('nombre_commit', '');
                 
                 if ($modulo !== '' || $nombreCommit !== '') {
                     if ($modulo !== '') {
                         $description = "Módulo: " . $modulo;
                         if ($nombreCommit !== '') {
                             $description .= " | Commit: " . $nombreCommit;
                         }
                     } else if ($nombreCommit !== '') {
                         $description = "Commit: " . $nombreCommit;
                     }
                 }
                 
                 // Procesar desarrollador y rama - siempre procesar
                 $desarrollador = $request->input('desarrollador', '');
                 $rama = $request->input('rama', '');
                 
                 if ($desarrollador !== '') {
                     // Obtener el nombre del desarrollador
                     $desarrolladorName = 'Desarrollador';
                     try {
                         $user = \App\Models\User::find($desarrollador);
                         if ($user) {
                             $desarrolladorName = $user->nickname ?? $user->name ?? 'Desarrollador';
                         }
                     } catch (\Exception $e) {
                         $desarrolladorName = 'Desarrollador';
                     }
                     
                     $notes = "Desarrollador: " . $desarrollador . " | Nombre: " . $desarrolladorName;
                     if ($rama !== '') {
                         $notes .= " | Rama: " . $rama;
                     }
                 }
                 
                 // Procesar repositorio - siempre procesar
                 $urlRepositorio = $request->input('url_repositorio', '');
                 if ($urlRepositorio !== '') {
                     $externalLink = $urlRepositorio;
                 }
                 
                 // Procesar fecha de desarrollo - siempre procesar
                 $fechaDesarrollo = $request->input('fecha_desarrollo', '');
                 if ($fechaDesarrollo !== '') {
                     $additionalComment = "Fecha de desarrollo: " . $fechaDesarrollo;
                 }
             } elseif ($request->phase_order == 6) {
                 // Fase 6 (Implementación) - Campos específicos de implementación
                 $description = $request->descripcion ?: '';
                 
                 if ($request->usuario || $request->contrasena) {
                     $notes = '';
                     if ($request->usuario) {
                         $notes .= "Usuario: " . $request->usuario;
                     }
                     if ($request->contrasena) {
                         if ($notes) $notes .= " | ";
                         $notes .= "Contraseña: " . $request->contrasena;
                     }
                 }
                 
                 if ($request->dominio || $request->url_completo) {
                     $additionalComment = '';
                     if ($request->dominio) {
                         $additionalComment .= "Dominio: " . $request->dominio;
                     }
                     if ($request->url_completo) {
                         if ($additionalComment) $additionalComment .= " | ";
                         $additionalComment .= "URL Completo: " . $request->url_completo;
                     }
                 }
                 
                 // El campo external_link se puede usar para la URL completa también
                 if ($request->url_completo) {
                     $externalLink = $request->url_completo;
                 }
             } else {
                 // Otras fases - Campos generales
                 $description = $request->descripcion ?: '';
                 $notes = $request->notas ?: '';
                 $externalLink = $request->enlace ?: '';
                 $additionalComment = $request->comentario_adicional ?: '';
             }
             
             // Actualizar la información del historial
             $updateData = [
                 'description' => $description,
                 'notes' => $notes,
                 'external_link' => $externalLink,
                 'additional_comment' => $additionalComment,
             ];
             
             // Actualizar fecha si se proporcionó
             if ($request->fecha_creacion) {
                 $updateData['start_date'] = $request->fecha_creacion;
                 $updateData['end_date'] = $request->fecha_creacion;
             }
             
             $historyItem->update($updateData);
             
             // Log de depuración después de la actualización
             Log::info('Datos actualizados en PhaseInfoHistory', [
                 'history_id' => $historyItem->id,
                 'update_data' => $updateData,
                 'final_data' => [
                     'description' => $historyItem->fresh()->description,
                     'notes' => $historyItem->fresh()->notes,
                     'external_link' => $historyItem->fresh()->external_link,
                     'additional_comment' => $historyItem->fresh()->additional_comment,
                 ]
             ]);
             
             // Log adicional para verificar que los campos se procesaron correctamente
             Log::info('Verificación de campos procesados para Fase 4', [
                 'phase_order' => $request->phase_order,
                 'modulo_value' => $request->input('modulo'),
                 'desarrollador_value' => $request->input('desarrollador'),
                 'fecha_desarrollo_value' => $request->input('fecha_desarrollo'),
                 'nombre_commit_value' => $request->input('nombre_commit'),
                 'url_repositorio_value' => $request->input('url_repositorio'),
                 'rama_value' => $request->input('rama'),
                 'description_final' => $description,
                 'notes_final' => $notes,
                 'external_link_final' => $externalLink,
                 'additional_comment_final' => $additionalComment,
             ]);
             
             // Actualizar documento si se proporcionó uno nuevo
             if ($request->hasFile('documento')) {
                 $document = $request->file('documento');
                 $documentName = 'phase_' . $historyItem->phase_id . '_updated_' . time() . '.' . $document->getClientOriginalExtension();
                 $documentPath = $document->storeAs('project_phases', $documentName, 'public');
                 
                 $historyItem->update([
                     'document_path' => $documentPath,
                 ]);
             }

             Log::info('Información del historial actualizada exitosamente', [
                 'history_id' => $historyItem->id,
                 'updated_by' => Auth::id(),
             ]);

             return response()->json([
                 'success' => true,
                 'message' => 'Información del historial actualizada exitosamente.'
             ]);

         } catch (\Exception $e) {
             Log::error('Error actualizando información del historial', [
                 'history_id' => $request->history_id,
                 'project_id' => $projectId,
                 'error' => $e->getMessage(),
             ]);

             return response()->json(['error' => 'Error al actualizar la información del historial.', 'message' => $e->getMessage()], 500);
         }
     }

     /**
      * Eliminar información del historial de una fase
      */
     public function deletePhaseInfo($projectId, $historyId)
     {
         $project = Project::findOrFail($projectId);
         
         // Verificar permisos
         if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
             abort(403, 'Solo el Scrum Master o un Admin pueden eliminar información del historial.');
         }

         try {
             $historyItem = PhaseInfoHistory::findOrFail($historyId);
             
             // Verificar que pertenezca al proyecto
             if ($historyItem->project_id != $projectId) {
                 return response()->json(['error' => 'El elemento del historial no pertenece a este proyecto.'], 400);
             }

             Log::info('Eliminando información del historial', [
                 'history_id' => $historyItem->id,
                 'project_id' => $projectId,
                 'phase_id' => $historyItem->phase_id,
                 'deleted_by' => Auth::id(),
             ]);

             // Eliminar el archivo físico si existe
             if ($historyItem->document_path && Storage::disk('public')->exists($historyItem->document_path)) {
                 Storage::disk('public')->delete($historyItem->document_path);
             }

             $historyItem->delete();

             Log::info('Información del historial eliminada exitosamente', [
                 'history_id' => $historyId,
                 'deleted_by' => Auth::id(),
             ]);

             return response()->json([
                 'success' => true,
                 'message' => 'Elemento del historial eliminado exitosamente.'
             ]);

         } catch (\Exception $e) {
             Log::error('Error eliminando información del historial', [
                 'history_id' => $historyId,
                 'project_id' => $projectId,
                 'error' => $e->getMessage(),
             ]);

             return response()->json(['error' => 'Error al eliminar la información del historial.', 'message' => $e->getMessage()], 500);
         }
     }

    /**
     * Obtener información de una fase para editar
     */
    public function getPhaseInfo(Request $request, $projectId, $historyId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar permisos
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden ver la información de las fases.');
        }

        try {
            $historyItem = PhaseInfoHistory::where('id', $historyId)
                ->where('project_id', $projectId)
                ->firstOrFail();

            // Obtener la información de la fase para incluir el phase_order
            $phase = ProjectPhase::where('id', $historyItem->phase_id)->first();
            
            // Agregar el phase_order a los datos de la fase
            $phaseData = $historyItem->toArray();
            $phaseData['phase_order'] = $phase ? $phase->order : null;

            return response()->json([
                'success' => true,
                'phase' => $phaseData
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo información de la fase', [
                'project_id' => $projectId,
                'history_id' => $historyId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al obtener información de la fase.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar información de una fase (nuevo método)
     */
    public function updatePhaseInfoNew(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar permisos
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden editar la información de las fases.');
        }

        $request->validate([
            'history_id' => 'required|exists:fabricasoft_phase_info_history,id',
            'descripcion' => 'nullable|string|max:1000',
            'notas' => 'nullable|string|max:500',
            'enlace' => 'nullable|url|max:500',
            'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'comentario_adicional' => 'nullable|string|max:500',
            'fecha_creacion' => 'nullable|date',
        ]);

        try {
            $historyItem = PhaseInfoHistory::where('id', $request->history_id)
                ->where('project_id', $projectId)
                ->firstOrFail();

            $updateData = [];
            
            // Actualizar campos si se proporcionaron
            if ($request->filled('descripcion')) {
                $updateData['description'] = $request->descripcion;
            }
            
            if ($request->filled('notas')) {
                $updateData['notes'] = $request->notas;
            }
            
            if ($request->filled('enlace')) {
                $updateData['external_link'] = $request->enlace;
            }

            if ($request->filled('comentario_adicional')) {
                $updateData['additional_comment'] = $request->comentario_adicional;
            }

            if ($request->filled('fecha_creacion')) {
                $updateData['start_date'] = $request->fecha_creacion;
                $updateData['end_date'] = $request->fecha_creacion;
            }

            // Actualizar documento si se subió uno nuevo
            if ($request->hasFile('documento')) {
                $document = $request->file('documento');
                $documentName = 'phase_' . $historyItem->phase_id . '_updated_' . time() . '.' . $document->getClientOriginalExtension();
                $documentPath = $document->storeAs('project_phases', $documentName, 'public');
                
                // Eliminar documento anterior si existe
                if ($historyItem->document_path) {
                    Storage::disk('public')->delete($historyItem->document_path);
                }
                
                $updateData['document_path'] = $documentPath;
            }

            // Actualizar el registro de historial
            $historyItem->update($updateData);

            Log::info('Información de la fase actualizada exitosamente', [
                'project_id' => $projectId,
                'phase_id' => $historyItem->phase_id,
                'history_id' => $historyItem->id,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Información de la fase actualizada exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando información de la fase', [
                'project_id' => $projectId,
                'history_id' => $request->history_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al actualizar la información de la fase.', 'message' => $e->getMessage()], 500);
        }
    }

         /**
     * Actualizar el estado del proyecto según el progreso de las fases
     */
    private function updateProjectStatus($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $phases = $project->phases()->orderBy('order')->get();
            
            // Contar fases completadas
            $completedPhases = $phases->where('status', 'completed')->count();
            $totalPhases = $phases->count();
            
            Log::info('Verificando estado del proyecto', [
                'project_id' => $projectId,
                'current_status' => $project->status,
                'completed_phases' => $completedPhases,
                'total_phases' => $totalPhases,
                'phases_details' => $phases->map(function($phase) {
                    return [
                        'id' => $phase->id,
                        'name' => $phase->phase_name,
                        'status' => $phase->status,
                        'order' => $phase->order
                    ];
                })
            ]);
            
            // Determinar el nuevo estado del proyecto
            $newStatus = $project->status; // Mantener el estado actual por defecto
            
            if ($completedPhases >= 7) {
                // Si se completaron 7 fases, el proyecto está completo
                $newStatus = 'completed';
            } elseif ($completedPhases >= 2) {
                // Si se completaron 2 o más fases, el proyecto está activo
                $newStatus = 'active';
            } elseif ($completedPhases >= 1) {
                // Si se completó al menos 1 fase, el proyecto está en planificación
                $newStatus = 'planning';
            }
            
            // Solo actualizar si hay un cambio de estado
            if ($newStatus !== $project->status) {
                $oldStatus = $project->status;
                $project->update(['status' => $newStatus]);
                
                Log::info('Estado del proyecto actualizado automáticamente', [
                    'project_id' => $projectId,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'completed_phases' => $completedPhases,
                    'total_phases' => $totalPhases
                ]);
            } else {
                Log::info('Estado del proyecto no cambió', [
                    'project_id' => $projectId,
                    'current_status' => $project->status,
                    'completed_phases' => $completedPhases,
                    'total_phases' => $totalPhases
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error actualizando estado del proyecto', [
                'project_id' => $projectId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Eliminar información de una fase (nuevo método)
     */
    public function deletePhaseInfoNew(Request $request, $projectId, $historyId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar permisos
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden eliminar la información de las fases.');
        }

        try {
            $historyItem = PhaseInfoHistory::where('id', $historyId)
                ->where('project_id', $projectId)
                ->firstOrFail();

            // Eliminar documento si existe
            if ($historyItem->document_path) {
                Storage::disk('public')->delete($historyItem->document_path);
            }

            // Eliminar el registro de historial
            $historyItem->delete();

            Log::info('Información de la fase eliminada exitosamente', [
                'project_id' => $projectId,
                'phase_id' => $historyItem->phase_id,
                'history_id' => $historyId,
                'deleted_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Información de la fase eliminada exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando información de la fase', [
                'project_id' => $projectId,
                'history_id' => $historyId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Error al eliminar la información de la fase.', 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Mostrar proyecto para el desarrollador
     */
    public function showProjectForDeveloper($id)
    {
        $developerId = Auth::id();
        
        // Verificar que el desarrollador sea miembro del equipo del proyecto
        $project = Project::whereHas('teamMembers', function($query) use ($developerId) {
            $query->where('user_id', $developerId)
                  ->where('status', 'active');
        })->with(['preregistration', 'phases', 'teamMembers', 'scrumMaster'])
          ->findOrFail($id);
        
        // Cargar información de las fases para cada fase completada
        foreach ($project->phases as $phase) {
            if ($phase->status === 'completed') {
                $phase->phaseInfoHistory = PhaseInfoHistory::where('project_id', $project->id)
                    ->where('phase_id', $phase->id)
                    ->with('addedByUser')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }
        
        return view('fabricasoft::desarrollador.show_project', compact('project'));
    }

    /**
     * Agregar información a una fase (para desarrolladores)
     */
    public function addPhaseInfoForDeveloper(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $developerId = Auth::id();
        
        // Verificar que el desarrollador sea miembro del equipo del proyecto
        if (!$project->teamMembers()->where('user_id', $developerId)->where('status', 'active')->exists()) {
            abort(403, 'No tienes permisos para agregar información a este proyecto.');
        }

        $request->validate([
            'phase_id' => 'required|exists:fabricasoft_project_phases,id',
            'nueva_descripcion' => 'required|string|max:1000',
            'nuevas_notas' => 'nullable|string|max:500',
            'nuevo_enlace' => 'nullable|url|max:500',
            'comentario_adicional' => 'nullable|string|max:500',
            'fecha_creacion' => 'required|date',
            'nuevo_documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        try {
            $documentPath = null;
            
            // Subir documento si se proporciona
            if ($request->hasFile('nuevo_documento')) {
                $documentPath = $request->file('nuevo_documento')->store('fabricasoft/phase_documents', 'public');
            }

            // Crear el registro de historial
            $historyItem = PhaseInfoHistory::create([
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'info_type' => 'complete_info', // Campo obligatorio
                'content' => $request->nueva_descripcion,
                'notes' => $request->nuevas_notas,
                'external_link' => $request->nuevo_enlace,
                'document_path' => $documentPath,
                'start_date' => $request->fecha_creacion,
                'additional_comment' => $request->comentario_adicional,
                'added_by' => $developerId, // Campo correcto según la migración
            ]);

            Log::info('Información de fase agregada por desarrollador', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'history_id' => $historyItem->id,
                'developer_id' => $developerId,
            ]);

            return redirect()->back()->with('success', 'Información agregada exitosamente a la fase.');

        } catch (\Exception $e) {
            Log::error('Error agregando información de fase por desarrollador', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'developer_id' => $developerId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Error al agregar información a la fase: ' . $e->getMessage());
        }
    }

    /**
     * Agregar información específica de fase para desarrolladores (Fases 4 y 6)
     */
    public function addPhaseInfoSpecific(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $developerId = Auth::id();
        
        // Verificar que el desarrollador sea miembro del equipo del proyecto
        if (!$project->teamMembers()->where('user_id', $developerId)->where('status', 'active')->exists()) {
            abort(403, 'No tienes permisos para agregar información a este proyecto.');
        }

        $request->validate([
            'phase_id' => 'required|exists:fabricasoft_project_phases,id',
            'phase_order' => 'required|integer|min:1|max:10',
        ]);

        try {
            $description = '';
            $notes = '';
            $externalLink = null;
            $additionalComment = '';

            // Procesar campos según el tipo de fase
            if ($request->phase_order == 4) {
                // Fase 4: Codificación
                $request->validate([
                    'modulo' => 'required|string|max:255',
                    'desarrollador' => 'required|exists:users,id',
                    'fecha_desarrollo' => 'required|date',
                    'nombre_commit' => 'required|string|max:255',
                    'url_repositorio' => 'required|url|max:500',
                    'rama' => 'nullable|string|max:100',
                ]);

                $description = "Módulo: {$request->modulo} | Commit: {$request->nombre_commit}";
                $notes = "Desarrollador: {$request->desarrollador} | Rama: " . ($request->rama ?: 'No especificada');
                $externalLink = $request->url_repositorio;
                $additionalComment = "Fecha de desarrollo: {$request->fecha_desarrollo}";

            } elseif ($request->phase_order == 6) {
                // Fase 6: Implementación
                $request->validate([
                    'descripcion' => 'required|string|max:1000',
                    'usuario' => 'required|string|max:255',
                    'contrasena' => 'required|string|max:255',
                    'dominio' => 'required|string|max:255',
                    'url_completo' => 'required|url|max:500',
                ]);

                $description = $request->descripcion;
                $notes = "Usuario: {$request->usuario} | Contraseña: {$request->contrasena}";
                $externalLink = $request->url_completo;
                $additionalComment = "Dominio: {$request->dominio} | URL Completo: {$request->url_completo}";

            } else {
                // Otras fases: campos generales
                $request->validate([
                    'descripcion' => 'required|string|max:1000',
                    'notas' => 'nullable|string|max:500',
                    'enlace' => 'nullable|url|max:500',
                    'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
                    'fecha_creacion' => 'nullable|date',
                    'comentario_adicional' => 'nullable|string|max:500',
                ]);

                $description = $request->descripcion;
                $notes = $request->notas;
                $externalLink = $request->enlace;
                $additionalComment = $request->comentario_adicional;
            }

            $documentPath = null;
            
            // Subir documento si se proporciona
            if ($request->hasFile('documento')) {
                $documentPath = $request->file('documento')->store('fabricasoft/phase_documents', 'public');
            }

            // Crear el registro de historial
            $historyItem = PhaseInfoHistory::create([
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'info_type' => 'complete_info',
                'content' => $description, // Campo requerido
                'description' => $description,
                'notes' => $notes,
                'external_link' => $externalLink,
                'document_path' => $documentPath,
                'start_date' => $request->fecha_creacion ?? now(),
                'additional_comment' => $additionalComment,
                'added_by' => $developerId,
            ]);

            Log::info('Información específica de fase agregada por desarrollador', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'phase_order' => $request->phase_order,
                'history_id' => $historyItem->id,
                'developer_id' => $developerId,
            ]);

            return redirect()->back()->with('success', 'Información agregada exitosamente a la fase.');

        } catch (\Exception $e) {
            Log::error('Error agregando información específica de fase por desarrollador', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'phase_order' => $request->phase_order,
                'developer_id' => $developerId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Error al agregar información a la fase: ' . $e->getMessage());
        }
    }

    /**
     * Obtener información de una fase para editar (para desarrolladores)
     */
    public function getPhaseInfoForDeveloper($projectId, $historyId)
    {
        $project = Project::findOrFail($projectId);
        $developerId = Auth::id();
        
        // Verificar que el desarrollador sea miembro del equipo del proyecto
        if (!$project->teamMembers()->where('user_id', $developerId)->where('status', 'active')->exists()) {
            abort(403, 'No tienes permisos para acceder a este proyecto.');
        }

        try {
            $historyItem = PhaseInfoHistory::where('id', $historyId)
                ->where('project_id', $projectId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $historyItem
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo información de fase para desarrollador', [
                'project_id' => $projectId,
                'history_id' => $historyId,
                'developer_id' => $developerId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de la fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar información de una fase (para desarrolladores)
     */
    public function updatePhaseInfoForDeveloper(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        $developerId = Auth::id();
        
        // Verificar que el desarrollador sea miembro del equipo del proyecto
        if (!$project->teamMembers()->where('user_id', $developerId)->where('status', 'active')->exists()) {
            abort(403, 'No tienes permisos para modificar este proyecto.');
        }

        $request->validate([
            'history_id' => 'required|exists:fabricasoft_phase_info_history,id',
            'descripcion' => 'required|string|max:1000',
            'notas' => 'nullable|string|max:500',
            'enlace' => 'nullable|url|max:500',
            'comentario_adicional' => 'nullable|string|max:500',
            'fecha_creacion' => 'required|date',
            'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        try {
            $historyItem = PhaseInfoHistory::where('id', $request->history_id)
                ->where('project_id', $projectId)
                ->firstOrFail();

            // Verificar que el desarrollador sea el creador de la información o tenga permisos especiales
            if ($historyItem->added_by !== $developerId && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
                abort(403, 'Solo puedes editar la información que has creado.');
            }

            $documentPath = $historyItem->document_path;
            
            // Subir nuevo documento si se proporciona
            if ($request->hasFile('documento')) {
                // Eliminar documento anterior si existe
                if ($documentPath) {
                    Storage::disk('public')->delete($documentPath);
                }
                $documentPath = $request->file('documento')->store('fabricasoft/phase_documents', 'public');
            }

            // Actualizar el registro
            $historyItem->update([
                'content' => $request->descripcion,
                'notes' => $request->notas,
                'external_link' => $request->enlace,
                'document_path' => $documentPath,
                'start_date' => $request->fecha_creacion,
                'additional_comment' => $request->comentario_adicional,
            ]);

            Log::info('Información de fase actualizada por desarrollador', [
                'project_id' => $projectId,
                'phase_id' => $historyItem->phase_id,
                'history_id' => $request->history_id,
                'developer_id' => $developerId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Información actualizada exitosamente.',
                'data' => $historyItem
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando información de fase por desarrollador', [
                'project_id' => $projectId,
                'history_id' => $request->history_id,
                'developer_id' => $developerId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la información: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar información de una fase (para desarrolladores)
     */
    public function deletePhaseInfoForDeveloper($projectId, $historyId)
    {
        $project = Project::findOrFail($projectId);
        $developerId = Auth::id();
        
        // Verificar que el desarrollador sea miembro del equipo del proyecto
        if (!$project->teamMembers()->where('user_id', $developerId)->where('status', 'active')->exists()) {
            abort(403, 'No tienes permisos para acceder a este proyecto.');
        }

        try {
            $historyItem = PhaseInfoHistory::where('id', $historyId)
                ->where('project_id', $projectId)
                ->firstOrFail();

            // Verificar que el desarrollador sea el creador de la información o tenga permisos especiales
            if ($historyItem->added_by !== $developerId && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
                abort(403, 'Solo puedes eliminar la información que has creado.');
            }

            // Eliminar documento si existe
            if ($historyItem->document_path) {
                Storage::disk('public')->delete($historyItem->document_path);
            }

            // Eliminar el registro de historial
            $historyItem->delete();

            Log::info('Información de la fase eliminada por desarrollador', [
                'project_id' => $projectId,
                'phase_id' => $historyItem->phase_id,
                'history_id' => $historyId,
                'developer_id' => $developerId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Información eliminada exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando información de fase por desarrollador', [
                'project_id' => $projectId,
                'history_id' => $historyId,
                'developer_id' => $developerId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la información: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Completar fase con información (versión simplificada que recibe phaseId del formulario)
     */
    public function completePhaseWithInfoSimple(Request $request, $projectId)
    {
        // Log de depuración
        Log::info('completePhaseWithInfoSimple llamado', [
            'project_id' => $projectId,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            Log::warning('Usuario no autorizado para completar fase con info', [
                'user_id' => Auth::id(),
                'scrum_master_id' => $project->scrum_master_id,
                'has_admin_role' => Auth::user()->hasCustomRole('fabricasoft.admin')
            ]);
            abort(403, 'Solo el Scrum Master o un Admin pueden completar fases.');
        }

        $phaseId = $request->input('phase_id');
        if (!$phaseId) {
            return response()->json([
                'success' => false,
                'message' => 'ID de fase no proporcionado'
            ], 400);
        }

        $phase = ProjectPhase::where('project_id', $projectId)
            ->where('id', $phaseId)
            ->firstOrFail();

        Log::info('Fase encontrada para completar con info', [
            'phase_id' => $phaseId,
            'phase_status' => $phase->status,
            'phase_name' => $phase->phase_name
        ]);

        // Validar la información según el tipo de fase
        $phaseOrder = $request->input('phase_order');
        
        if ($phaseOrder == 4) {
            // Validación para Fase 4 (Codificación)
            $request->validate([
                'desarrollador' => 'required|exists:users,id',
                'modulo' => 'required|string|max:200',
                'fecha_desarrollo' => 'required|date',
                'nombre_commit' => 'required|string|max:200',
                'url_repositorio' => 'required|url|max:500',
                'rama' => 'nullable|string|max:100',
            ]);
        } elseif ($phaseOrder == 6) {
            // Validación para Fase 6 (Implementación)
            $request->validate([
                'descripcion' => 'required|string|max:1000',
                'usuario' => 'required|string|max:200',
                'contrasena' => 'required|string|max:200',
                'dominio' => 'required|string|max:200',
                'url_completo' => 'required|url|max:500',
                'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            ]);
        } else {
            // Validación para otras fases (campos generales)
            $request->validate([
                'descripcion' => 'required|string|max:1000',
                'notas' => 'nullable|string|max:500',
                'enlace' => 'nullable|url|max:500',
                'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
                'comentario_adicional' => 'nullable|string|max:500',
                'fecha_creacion' => 'nullable|date',
            ]);
        }

        try {
            // Procesar documento si se subió
            $documentPath = null;
            if ($request->hasFile('documento')) {
                $document = $request->file('documento');
                $documentName = time() . '_' . $document->getClientOriginalName();
                $documentPath = $document->storeAs('fabricasoft/documents', $documentName, 'public');
            }

            // Crear registro en PhaseInfoHistory con la información de cierre
            if ($phaseOrder == 4) {
                // Para Fase 4, usar los campos específicos de desarrollo
                PhaseInfoHistory::create([
                    'project_id' => $projectId,
                    'phase_id' => $phaseId,
                    'info_type' => 'complete_info',
                    'content' => 'Información de cierre de fase de codificación',
                    'description' => "Módulo: " . $request->modulo . " | Commit: " . $request->nombre_commit,
                    'notes' => "Desarrollador: " . $request->desarrollador . " | Rama: " . ($request->rama ?: 'No especificada'),
                    'external_link' => $request->url_repositorio,
                    'document_path' => $documentPath,
                    'additional_comment' => "Fecha de desarrollo: " . $request->fecha_desarrollo,
                    'start_date' => $request->fecha_desarrollo,
                    'end_date' => $request->fecha_desarrollo,
                    'added_by' => Auth::id(),
                ]);
            } else {
                // Para otras fases, usar los campos generales
                PhaseInfoHistory::create([
                    'project_id' => $projectId,
                    'phase_id' => $phaseId,
                    'info_type' => 'complete_info',
                    'content' => 'Información de cierre de fase',
                    'description' => $request->descripcion,
                    'notes' => $request->notas,
                    'external_link' => $request->enlace,
                    'document_path' => $documentPath,
                    'additional_comment' => $request->comentario_adicional,
                    'start_date' => $request->fecha_creacion ? $request->fecha_creacion : now()->toDateString(),
                    'end_date' => $request->fecha_creacion ? $request->fecha_creacion : now()->toDateString(),
                    'added_by' => Auth::id(),
                ]);
            }

            // Marcar la fase como completada
            $phase->update([
                'status' => 'completed',
                'completed_at' => now(),
                'completed_by' => Auth::id()
            ]);

            // Habilitar la siguiente fase si existe
            $nextPhase = ProjectPhase::where('project_id', $projectId)
                ->where('order', $phase->order + 1)
                ->first();

            if ($nextPhase) {
                $nextPhase->update([
                    'status' => 'planned',
                    'enabled_at' => now(),
                    'enabled_by' => Auth::id()
                ]);

                Log::info('Siguiente fase habilitada', [
                    'project_id' => $projectId,
                    'current_phase_id' => $phaseId,
                    'next_phase_id' => $nextPhase->id,
                    'next_phase_order' => $nextPhase->order
                ]);
            } else {
                // Si no hay siguiente fase, es la última (Implementación - orden 6)
                // Liberar al Scrum Master para el próximo proyecto
                if ($phase->order == 6) {
                    Log::info('Última fase completada - Scrum Master liberado', [
                        'project_id' => $projectId,
                        'phase_id' => $phaseId,
                        'phase_order' => $phase->order,
                        'scrum_master_id' => $project->scrum_master_id
                    ]);
                    
                    // Marcar el proyecto como completado
                    $project->update([
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);
                }
            }

            Log::info('Fase completada exitosamente con información', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'phase_order' => $phaseOrder,
                'user_id' => Auth::id()
            ]);

            $response = [
                'success' => true,
                'message' => 'Fase completada exitosamente con la información proporcionada.',
                'next_phase' => $nextPhase ? [
                    'id' => $nextPhase->id,
                    'order' => $nextPhase->order,
                    'name' => $nextPhase->phase_name
                ] : null
            ];

            // Si es la última fase, agregar mensaje especial
            if ($phase->order == 6 && !$nextPhase) {
                $response['message'] = '¡Proyecto completado exitosamente! El Scrum Master ha sido liberado para el próximo proyecto.';
                $response['project_completed'] = true;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error completando fase con información', [
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'phase_order' => $phaseOrder,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al completar la fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Entregar proyecto cuando todas las fases estén completadas
     */
    public function entregarProyecto(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Verificar que el usuario autenticado sea el Scrum Master o un Admin
        if (Auth::id() !== $project->scrum_master_id && !Auth::user()->hasCustomRole('fabricasoft.admin')) {
            abort(403, 'Solo el Scrum Master o un Admin pueden entregar el proyecto.');
        }

        try {
            // Verificar que todas las fases estén completadas
            $totalPhases = $project->phases->count();
            $completedPhases = $project->phases()->where('status', 'completed')->count();
            
            if ($completedPhases < $totalPhases) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede entregar el proyecto. Todas las fases deben estar completadas.'
                ], 400);
            }

            // Iniciar transacción para asegurar consistencia de datos
            \DB::beginTransaction();

            try {
                            // 1. Marcar el proyecto como entregado (usar 'completed' en lugar de 'delivered')
            $project->update([
                'status' => 'completed',
                'delivered_at' => now(),
                'delivered_by' => Auth::id()
            ]);

            // 2. Marcar todas las fases como entregadas (mantener 'completed')
            // Las fases ya están como 'completed', no necesitamos cambiarlas

                // 3. Liberar al Scrum Master - Marcar como disponible para nuevos proyectos
                if ($project->scrum_master_id) {
                    // El Scrum Master ya no está asignado a este proyecto
                    // Su estado se mantiene como 'available' en el sistema
                    Log::info('Scrum Master liberado del proyecto', [
                        'project_id' => $projectId,
                        'scrum_master_id' => $project->scrum_master_id,
                        'status' => 'liberated_for_new_projects'
                    ]);
                }

                // 4. Liberar a todos los miembros del equipo
                $teamMembers = $project->teamMembers()->with('user')->get();
                
                foreach ($teamMembers as $member) {
                    // Marcar como inactivo
                    $member->update(['status' => 'inactive']);
                }
                
                // Registrar información de participación en el log para auditoría
                Log::info('Participantes del proyecto entregado', [
                    'project_id' => $projectId,
                    'scrum_master' => [
                        'user_id' => $project->scrum_master_id,
                        'role' => 'scrum_master',
                        'responsibilities' => 'Liderazgo del equipo y gestión del proyecto'
                    ],
                    'team_members' => $teamMembers->map(function($member) {
                        return [
                            'user_id' => $member->user_id,
                            'role' => $member->role,
                            'responsibilities' => $member->responsibilities
                        ];
                    })->toArray()
                ]);

                // 5. Crear registro de entrega del proyecto (usar tabla existente o logging)
                // Por ahora, solo registramos en el log ya que la tabla puede no existir
                Log::info('Registro de entrega del proyecto', [
                    'project_id' => $projectId,
                    'delivered_by' => Auth::id(),
                    'delivered_at' => now(),
                    'delivery_notes' => 'Proyecto entregado exitosamente. Todos los miembros del equipo han sido liberados.'
                ]);

                // 6. Notificar a todos los stakeholders (opcional - puedes implementar notificaciones aquí)
                $this->notifyProjectDelivery($project);

                \DB::commit();

                Log::info('Proyecto entregado exitosamente con liberación de equipo', [
                    'project_id' => $projectId,
                    'delivered_by' => Auth::id(),
                    'delivered_at' => now(),
                    'scrum_master_liberated' => $project->scrum_master_id,
                    'team_members_liberated' => $project->teamMembers()->where('status', 'inactive')->count(),
                    'total_participants' => $teamMembers->count() + ($project->scrum_master_id ? 1 : 0)
                ]);

                return response()->json([
                    'success' => true,
                    'message' => '🎉 ¡PROYECTO ENTREGADO EXITOSAMENTE! 🎉\n\nEl proyecto ha sido marcado como finalizado y entregado.\n\n✅ Scrum Master liberado para nuevos proyectos\n✅ Equipo de desarrollo liberado\n✅ Todas las fases completadas\n\nEl Scrum Master ya puede ser asignado a nuevos proyectos.',
                    'delivery_details' => [
                        'project_status' => 'completed',
                        'phases_status' => 'completed',
                        'team_liberated' => true,
                        'scrum_master_liberated' => true
                    ]
                ]);

            } catch (\Exception $e) {
                \DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error entregando proyecto', [
                'project_id' => $projectId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al entregar el proyecto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notificar entrega del proyecto a todos los stakeholders
     */
    private function notifyProjectDelivery($project)
    {
        try {
            // Obtener todos los usuarios involucrados en el proyecto
            $stakeholders = collect();
            
            // Agregar Scrum Master
            if ($project->scrum_master_id) {
                $stakeholders->push(\App\Models\User::find($project->scrum_master_id));
            }
            
            // Agregar miembros del equipo
            $teamMembers = $project->teamMembers()->with('user')->get();
            foreach ($teamMembers as $member) {
                if ($member->user) {
                    $stakeholders->push($member->user);
                }
            }
            
            // Agregar cliente (si existe)
            if ($project->preregistration && $project->preregistration->user) {
                $stakeholders->push($project->preregistration->user);
            }
            
            // Filtrar usuarios únicos
            $stakeholders = $stakeholders->unique('id')->filter();
            
            // Aquí puedes implementar el sistema de notificaciones
            // Por ejemplo, enviar emails, crear notificaciones en base de datos, etc.
            foreach ($stakeholders as $stakeholder) {
                Log::info('Notificando entrega del proyecto a stakeholder', [
                    'project_id' => $project->id,
                    'stakeholder_id' => $stakeholder->id,
                    'stakeholder_email' => $stakeholder->email
                ]);
                
                // TODO: Implementar sistema de notificaciones
                // Mail::to($stakeholder->email)->send(new ProjectDeliveredMail($project));
                // Notification::send($stakeholder, new ProjectDeliveredNotification($project));
            }
            
        } catch (\Exception $e) {
            Log::error('Error notificando entrega del proyecto', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
