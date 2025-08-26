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
use Modules\FABRICASOFT\Entities\Project;

class AnalystController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard del analista
     */
    public function dashboard()
    {
        $analystId = Auth::id();
        
        $stats = [
            'total_assigned' => Preregistration::where('assigned_analyst_id', $analystId)->count(),
            'pending_analysis' => Preregistration::where('assigned_analyst_id', $analystId)
                ->where('workflow_status', 'assigned')->count(),
            'in_progress' => Preregistration::where('assigned_analyst_id', $analystId)
                ->where('workflow_status', 'analysis')->count(),
            'completed' => Preregistration::where('assigned_analyst_id', $analystId)
                ->where('workflow_status', 'srs_ready')->count(),
        ];

        $assignedRequests = Preregistration::where('assigned_analyst_id', $analystId)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('fabricasoft::analyst.dashboard', compact('stats', 'assignedRequests'));
    }

    /**
     * Mostrar solicitudes asignadas al analista
     */
    public function assignedRequests(Request $request)
    {
        $analystId = Auth::id();
        
        // Iniciar query base
        $query = Preregistration::where('assigned_analyst_id', $analystId);
        
        // Aplicar filtros si están presentes
        if ($request->filled('status')) {
            $query->where('analysis_status', $request->status);
        }
        
        if ($request->filled('software_type')) {
            $query->where('software_type', $request->software_type);
        }
        
        if ($request->filled('date_from')) {
            $query->where('assigned_at', '>=', $request->date_from);
        }
        
        // Ordenar y paginar
        $requests = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Agregar los parámetros de filtro a la paginación
        $requests->appends($request->query());
        
        return view('fabricasoft::analyst.assigned_requests', compact('requests'));
    }

    /**
     * Obtener tipos de software válidos para el sistema
     */
    private function getValidSoftwareTypes()
    {
        return [
            'Sistema Web' => 'Sistema Web',
            'Aplicación Móvil' => 'Aplicación Móvil',
            'Sistema de Escritorio' => 'Sistema de Escritorio',
            'API' => 'API',
            'Otro' => 'Otro'
        ];
    }

    /**
     * Mostrar detalles de una solicitud específica
     */
    public function showRequest($id)
    {
        $analystId = Auth::id();
        $request = Preregistration::where('id', $id)
            ->where('assigned_analyst_id', $analystId)
            ->firstOrFail();
        
        return view('fabricasoft::analyst.show_request', compact('request'));
    }

    /**
     * Iniciar análisis de una solicitud
     */
    public function startAnalysis(Request $request, $id)
    {
        $analystId = Auth::id();
        $solicitud = Preregistration::where('id', $id)
            ->where('assigned_analyst_id', $analystId)
            ->firstOrFail();

        $solicitud->update([
            'workflow_status' => 'analysis',
            'analysis_status' => 'in_progress',
            'analysis_started_at' => now(),
        ]);

        Log::info('FABRICASOFT análisis iniciado', [
            'solicitud_id' => $solicitud->id,
            'analyst_id' => $analystId,
        ]);

        return redirect()->route('fabricasoft.analyst.show.request', $solicitud->id)
            ->with('success', 'Análisis iniciado exitosamente.');
    }

    /**
     * Subir SRS para una solicitud
     */
    public function uploadSRS(Request $request, $id)
    {
        $analystId = Auth::id();
        $solicitud = Preregistration::where('id', $id)
            ->where('assigned_analyst_id', $analystId)
            ->firstOrFail();

        $request->validate([
            'srs_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'analysis_notes' => 'nullable|string|max:2000',
        ]);

        try {
            $file = $request->file('srs_file');
            $filename = 'srs_' . $solicitud->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/srs', $filename);

            $solicitud->update([
                'srs_file_path' => $filePath,
                'analysis_notes' => $request->analysis_notes,
                'workflow_status' => 'srs_ready',
                'analysis_status' => 'completed',
                'srs_uploaded_at' => now(),
            ]);

            Log::info('FABRICASOFT SRS subido', [
                'solicitud_id' => $solicitud->id,
                'analyst_id' => $analystId,
                'file_path' => $filePath,
            ]);

            return redirect()->route('fabricasoft.analyst.show.request', $solicitud->id)
                ->with('success', 'SRS subido exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error subiendo SRS', [
                'solicitud_id' => $solicitud->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Error al subir el archivo: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar notas de análisis
     */
    public function updateNotes(Request $request, $id)
    {
        $analystId = Auth::id();
        $solicitud = Preregistration::where('id', $id)
            ->where('assigned_analyst_id', $analystId)
            ->firstOrFail();

        $request->validate([
            'analysis_notes' => 'required|string|max:2000',
        ]);

        $solicitud->update([
            'analysis_notes' => $request->analysis_notes,
            'updated_at' => now(),
        ]);

        return redirect()->route('fabricasoft.analyst.show.request', $solicitud->id)
            ->with('success', 'Notas actualizadas exitosamente.');
    }

    /**
     * Descargar archivo SRS
     */
    public function downloadSRS($id)
    {
        try {
            $analystId = Auth::id();
            $solicitud = Preregistration::where('id', $id)
                ->where('assigned_analyst_id', $analystId)
                ->firstOrFail();
            
            if (!$solicitud->srs_file_path) {
                abort(404, 'Archivo SRS no encontrado.');
            }

            // Corregir la ruta del archivo - eliminar "public/" duplicado si existe
            $filePath = $solicitud->srs_file_path;
            if (str_starts_with($filePath, 'public/')) {
                $filePath = substr($filePath, 7); // Remover "public/" del inicio
            }
            
            $fullPath = storage_path('app/public/' . $filePath);
            
            if (!file_exists($fullPath)) {
                // Intentar con la ruta original como respaldo
                $originalPath = storage_path('app/' . $solicitud->srs_file_path);
                
                if (file_exists($originalPath)) {
                    return response()->download($originalPath);
                }
                
                abort(404, 'Archivo SRS no encontrado en el servidor.');
            }

            return response()->download($fullPath);
            
        } catch (\Exception $e) {
            abort(500, 'Error interno del servidor al descargar el archivo.');
        }
    }

    /**
     * Mostrar proyectos asignados al analista
     */
    public function assignedProjects()
    {
        $analystId = Auth::id();
        
        $projects = Project::where('scrum_master_id', $analystId)
            ->with(['preregistration', 'teamMembers'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('fabricasoft::analyst.projects', compact('projects'));
    }

    /**
     * Mostrar detalles de un proyecto específico
     */
    public function showProject($id)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $id)
            ->where('scrum_master_id', $analystId)
            ->with(['preregistration', 'teamMembers', 'phases'])
            ->firstOrFail();
        
        // Cargar información del historial de las fases
        foreach ($project->phases as $phase) {
            $phase->phaseInfoHistory = DB::table('fabricasoft_phase_info_history')
                ->where('project_id', $id)
                ->where('phase_id', $phase->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        // Obtener usuarios disponibles para agregar al equipo (excluyendo los que ya están)
        $availableUsers = \App\Models\User::whereHas('roles', function($query) {
            $query->whereIn('slug', [
                'fabricasoft.desarrollador',
                'fabricasoft.analista'
            ]);
        })->whereNotIn('id', function($subquery) use ($id) {
            $subquery->select('user_id')
                ->from('fabricasoft_project_team_members')
                ->where('project_id', $id)
                ->where('status', '!=', 'removed');
        })->get();
        
        return view('fabricasoft::analyst.show_project', compact('project', 'availableUsers'));
    }

    /**
     * Agregar información de fase
     */
    public function addPhaseInfo(Request $request, $projectId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        $request->validate([
            'phase_id' => 'required|integer|exists:fabricasoft_project_phases,id',
            'nueva_descripcion' => 'required|string|max:2000',
            'nuevo_documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'nuevas_notas' => 'nullable|string|max:500',
            'comentario_adicional' => 'nullable|string|max:500',
            'nuevo_enlace' => 'nullable|url|max:255',
            'fecha_creacion' => 'nullable|date',
        ]);

        try {
            $phaseId = $request->phase_id;
            
            // Procesar documento si se subió
            $documentPath = null;
            if ($request->hasFile('nuevo_documento')) {
                $file = $request->file('nuevo_documento');
                $fileName = 'fase_info_' . time() . '_' . $file->getClientOriginalName();
                $documentPath = $file->storeAs('fabricasoft/proyectos/' . $project->id . '/fase_info', $fileName, 'public');
            }

            // Insertar información de la fase - solo usar columnas que existen
                           DB::table('fabricasoft_phase_info_history')->insert([
                   'project_id' => $projectId,
                   'phase_id' => $phaseId,
                   'info_type' => 'complete_info',
                   'content' => $request->nueva_descripcion,
                   'description' => $request->nueva_descripcion,
                   'notes' => $request->nuevas_notas,
                   'external_link' => $request->nuevo_enlace,
                   'start_date' => $request->fecha_creacion,
                   'additional_comment' => $request->comentario_adicional,
                   'document_path' => $documentPath,
                   'added_by' => $analystId,
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);

            return response()->json(['success' => true, 'message' => 'Información de fase agregada exitosamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al agregar información: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Agregar información específica de fase para analistas (Fases 4 y 6)
     */
    public function addPhaseInfoSpecific(Request $request, $projectId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        $request->validate([
            'phase_id' => 'required|integer|exists:fabricasoft_project_phases,id',
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
                $file = $request->file('documento');
                $fileName = 'fase_info_' . time() . '_' . $file->getClientOriginalName();
                $documentPath = $file->storeAs('fabricasoft/proyectos/' . $project->id . '/fase_info', $fileName, 'public');
            }

            // Insertar información de la fase
            DB::table('fabricasoft_phase_info_history')->insert([
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
                'added_by' => $analystId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Información específica de fase agregada por analista', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'phase_order' => $request->phase_order,
                'analyst_id' => $analystId,
            ]);

            return redirect()->back()->with('success', 'Información de fase agregada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error agregando información específica de fase por analista', [
                'project_id' => $projectId,
                'phase_id' => $request->phase_id,
                'phase_order' => $request->phase_order,
                'analyst_id' => $analystId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Error al agregar información: ' . $e->getMessage());
        }
    }

    /**
     * Obtener información de fase para edición
     */
    public function getPhaseInfoForEdit($projectId, $phaseId, $infoId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        $phaseInfo = DB::table('fabricasoft_phase_info_history')
            ->where('id', $infoId)
            ->where('project_id', $projectId)
            ->where('phase_id', $phaseId)
            ->first();

        if (!$phaseInfo) {
            abort(404, 'Información de fase no encontrada.');
        }

        return response()->json($phaseInfo);
    }

    /**
     * Editar información de fase
     */
    public function editPhaseInfo(Request $request, $projectId, $phaseId, $historyId)
    {
        try {
            \Log::info('editPhaseInfo called with projectId: ' . $projectId . ', phaseId: ' . $phaseId . ', historyId: ' . $historyId);
            
            // Verificar autenticación
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Usuario no autenticado'], 401);
            }
            
            $analystId = Auth::id();
            
            // Verificar si el proyecto existe y pertenece al analista
            $project = Project::where('id', $projectId)
                ->where('scrum_master_id', $analystId)
                ->first();
                
            if (!$project) {
                return response()->json(['success' => false, 'message' => 'Proyecto no encontrado o no tienes permisos'], 404);
            }

            // Log de los datos recibidos
            \Log::info('Datos recibidos en editPhaseInfo:', $request->all());
            
            // Validar datos de entrada
            try {
                $request->validate([
                    'nueva_descripcion' => 'required|string|max:2000',
                    'nuevas_notas' => 'nullable|string|max:500',
                    'nuevo_enlace' => 'nullable|url|max:255',
                    'fecha_creacion' => 'nullable|date',
                    'comentario_adicional' => 'nullable|string|max:500',
                    'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
                ]);
                
                \Log::info('Validación exitosa en editPhaseInfo');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Error de validación en editPhaseInfo:', $e->errors());
                return response()->json([
                    'success' => false, 
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            // Obtener el registro actual
            try {
                \Log::info('Buscando registro en fabricasoft_phase_info_history con ID: ' . $historyId);
                
                $currentRecord = DB::table('fabricasoft_phase_info_history')
                    ->where('id', $historyId)
                    ->where('project_id', $projectId)
                    ->where('phase_id', $phaseId)
                    ->first();

                if (!$currentRecord) {
                    \Log::warning('Registro no encontrado en fabricasoft_phase_info_history');
                    return response()->json(['success' => false, 'message' => 'Registro no encontrado'], 404);
                }
                
                \Log::info('Registro encontrado: ' . json_encode($currentRecord));
            } catch (\Exception $e) {
                \Log::error('Error al buscar registro: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Error al buscar registro: ' . $e->getMessage()], 500);
            }

            // Procesar documento si se subió uno nuevo
            $documentPath = $currentRecord->document_path; // Mantener el existente por defecto
            if ($request->hasFile('documento')) {
                $file = $request->file('documento');
                $fileName = 'fase_info_edit_' . time() . '_' . $file->getClientOriginalName();
                $documentPath = $file->storeAs('fabricasoft/proyectos/' . $project->id . '/fase_info', $fileName, 'public');
            }

            // Actualizar información de la fase
            try {
                \Log::info('Actualizando registro en fabricasoft_phase_info_history');
                
                $updateResult = DB::table('fabricasoft_phase_info_history')
                    ->where('id', $historyId)
                    ->where('project_id', $projectId)
                    ->where('phase_id', $phaseId)
                    ->update([
                        'content' => $request->nueva_descripcion,
                        'description' => $request->nueva_descripcion, // También actualizar description
                        'notes' => $request->nuevas_notas,
                        'external_link' => $request->nuevo_enlace,
                        'start_date' => $request->fecha_creacion,
                        'additional_comment' => $request->comentario_adicional,
                        'document_path' => $documentPath,
                        'updated_at' => now(),
                    ]);
                
                \Log::info('Registro actualizado. Filas afectadas: ' . $updateResult);
                
                if ($updateResult === 0) {
                    \Log::warning('No se actualizó ninguna fila');
                    return response()->json(['success' => false, 'message' => 'No se pudo actualizar el registro'], 500);
                }
            } catch (\Exception $e) {
                \Log::error('Error al actualizar registro: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Error al actualizar registro: ' . $e->getMessage()], 500);
            }

            return response()->json(['success' => true, 'message' => 'Información de fase actualizada exitosamente']);

        } catch (\Exception $e) {
            \Log::error('Error al editar información de fase: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar información de fase (con 3 parámetros)
     */
    public function deletePhaseInfo($projectId, $phaseId, $infoId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        try {
            DB::table('fabricasoft_phase_info_history')
                ->where('id', $infoId)
                ->where('project_id', $projectId)
                ->where('phase_id', $phaseId)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Información de fase eliminada exitosamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar información: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar información de fase (con 2 parámetros - para compatibilidad)
     */
    public function deletePhaseInfoSimple($projectId, $historyId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        try {
            DB::table('fabricasoft_phase_info_history')
                ->where('id', $historyId)
                ->where('project_id', $projectId)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Información de fase eliminada exitosamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar información: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Agregar miembro al equipo del proyecto
     */
    public function addTeamMember(Request $request, $projectId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:100',
        ]);

        try {
            DB::table('fabricasoft_project_team_members')->insert([
                'project_id' => $projectId,
                'user_id' => $request->user_id,
                'role' => $request->role,
                'assigned_by' => $analystId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Miembro agregado al equipo exitosamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al agregar miembro: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remover miembro del equipo del proyecto
     */
    public function removeTeamMember($projectId, $memberId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        try {
            DB::table('fabricasoft_project_team_members')
                ->where('id', $memberId)
                ->where('project_id', $projectId)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Miembro removido del equipo exitosamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al remover miembro: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Completar fase con información
     */
    public function completePhaseWithInfo(Request $request, $projectId, $phaseId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        $request->validate([
            'nueva_descripcion' => 'required|string|max:2000',
            'nuevas_notas' => 'nullable|string|max:1000',
            'comentario_adicional' => 'nullable|string|max:1000',
            'nuevo_enlace' => 'nullable|url|max:255',
            'fecha_creacion' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Agregar información de la fase - solo usar columnas que existen
            DB::table('fabricasoft_phase_info_history')->insert([
                'project_id' => $projectId,
                'phase_id' => $phaseId,
                'info_type' => 'completion',
                'content' => $request->nueva_descripcion,
                'notes' => $request->nuevas_notas,
                'additional_comment' => $request->comentario_adicional,
                'external_link' => $request->nuevo_enlace,
                'added_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Marcar la fase como completada
            DB::table('fabricasoft_project_phases')
                ->where('id', $phaseId)
                ->where('project_id', $projectId)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Fase completada exitosamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al completar fase: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Iniciar fase
     */
    public function startPhase(Request $request, $projectId, $phaseId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        try {
            DB::table('fabricasoft_project_phases')
                ->where('id', $phaseId)
                ->where('project_id', $projectId)
                ->update([
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Fase iniciada exitosamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al iniciar fase: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar información de fase
     */
    public function updatePhaseInfo(Request $request, $projectId)
    {
        $analystId = Auth::id();
        $project = Project::where('id', $projectId)
            ->where('scrum_master_id', $analystId)
            ->firstOrFail();

        $request->validate([
            'phase_id' => 'required|integer',
            'nueva_descripcion' => 'required|string|max:2000',
            'nuevas_notas' => 'nullable|string|max:500',
            'comentario_adicional' => 'nullable|string|max:500',
            'nuevo_enlace' => 'nullable|url|max:255',
        ]);

        try {
            DB::table('fabricasoft_phase_info_history')
                ->where('project_id', $projectId)
                ->where('phase_id', $request->phase_id)
                ->where('added_by', $analystId)
                ->update([
                    'content' => $request->nueva_descripcion,
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Información de fase actualizada exitosamente.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar información: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtener información de fase
     */
    public function getPhaseInfo($projectId, $historyId)
    {
        try {
            $analystId = Auth::id();
            $project = Project::where('id', $projectId)
                ->where('scrum_master_id', $analystId)
                ->firstOrFail();

            $phaseInfo = DB::table('fabricasoft_phase_info_history')
                ->where('id', $historyId)
                ->where('project_id', $projectId)
                ->first();

            if (!$phaseInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Información de fase no encontrada.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $phaseInfo->id,
                    'content' => $phaseInfo->content,
                    'description' => $phaseInfo->description,
                    'notes' => $phaseInfo->notes,
                    'external_link' => $phaseInfo->external_link,
                    'start_date' => $phaseInfo->start_date,
                    'additional_comment' => $phaseInfo->additional_comment,
                    'document_path' => $phaseInfo->document_path,
                    'created_at' => $phaseInfo->created_at,
                    'updated_at' => $phaseInfo->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al obtener información de fase: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de fase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar información general de fase
     */
    public function savePhase(Request $request, $projectId)
    {
        try {
            $analystId = Auth::id();
            $project = Project::where('id', $projectId)
                ->where('scrum_master_id', $analystId)
                ->firstOrFail();
            
            // Validar datos
            $request->validate([
                'phase_id' => 'required|integer|exists:fabricasoft_project_phases,id',
                'phase_title' => 'required|string|max:255',
                'phase_description' => 'required|string|max:2000',
                'phase_notes' => 'nullable|string|max:500',
                'phase_link' => 'nullable|url|max:255',
                'phase_document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
                'phase_status' => 'required|in:in_progress,completed',
            ]);
            
            $phaseId = $request->phase_id;
            
            // Procesar documento si se subió
            $documentPath = null;
            if ($request->hasFile('phase_document')) {
                $file = $request->file('phase_document');
                $fileName = 'fase_' . time() . '_' . $file->getClientOriginalName();
                $documentPath = $file->storeAs('fabricasoft/proyectos/' . $project->id . '/fases', $fileName, 'public');
            }
            
            // Actualizar la fase
            DB::table('fabricasoft_project_phases')
                ->where('id', $phaseId)
                ->where('project_id', $projectId)
                ->update([
                    'title' => $request->phase_title,
                    'description' => $request->phase_description,
                    'notes' => $request->phase_notes,
                    'external_link' => $request->phase_link,
                    'document_path' => $documentPath,
                    'status' => $request->phase_status,
                    'updated_at' => now(),
                ]);
            
            // Crear registro en el historial - solo usar columnas que existen
            \Modules\FABRICASOFT\Entities\PhaseInfoHistory::create([
                'project_id' => $project->id,
                'phase_id' => $phaseId,
                'info_type' => 'phase_update',
                'content' => $request->phase_description,
                'document_path' => $documentPath,
                'added_by' => $analystId,
            ]);
            
            return response()->json([
                'success' => true, 
                'message' => 'Información de fase guardada exitosamente.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al guardar información de fase: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar la información: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar información de la Fase 2 (Análisis y SRS)
     */
    public function savePhase2(Request $request, $projectId)
    {
        try {
            $analystId = Auth::id();
            $project = Project::where('id', $projectId)
                ->where('scrum_master_id', $analystId)
                ->firstOrFail();
            
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
            
            // Crear registro en el historial - solo usar columnas que existen
            \Modules\FABRICASOFT\Entities\PhaseInfoHistory::create([
                'project_id' => $project->id,
                'phase_id' => $phase2->id,
                'info_type' => 'phase2_completion',
                'content' => $request->descripcion,
                'document_path' => $documentPath,
                'added_by' => $analystId,
            ]);
            
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
     * Actualizar información de una fase (nuevo método para analista)
     */
    public function updatePhaseInfoNew(Request $request, $projectId)
    {
        try {
            $analystId = Auth::id();
            $project = Project::where('id', $projectId)
                ->where('scrum_master_id', $analystId)
                ->firstOrFail();

            $request->validate([
                'history_id' => 'required|exists:fabricasoft_phase_info_history,id',
                'descripcion' => 'nullable|string|max:1000',
                'notas' => 'nullable|string|max:500',
                'enlace' => 'nullable|url|max:500',
                'documento' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
                'comentario_adicional' => 'nullable|string|max:500',
                'fecha_creacion' => 'nullable|date',
            ]);

            $historyItem = DB::table('fabricasoft_phase_info_history')
                ->where('id', $request->history_id)
                ->where('project_id', $projectId)
                ->first();

            if (!$historyItem) {
                return response()->json(['success' => false, 'message' => 'Registro no encontrado'], 404);
            }

            $updateData = [];
            
            // Actualizar campos si se proporcionaron
            if ($request->filled('descripcion')) {
                $updateData['description'] = $request->descripcion;
                $updateData['content'] = $request->descripcion; // También actualizar content
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
            }

            // Actualizar documento si se subió uno nuevo
            if ($request->hasFile('documento')) {
                $document = $request->file('documento');
                $documentName = 'phase_' . $historyItem->phase_id . '_updated_' . time() . '.' . $document->getClientOriginalExtension();
                $documentPath = $document->storeAs('fabricasoft/proyectos/' . $project->id . '/fase_info', $documentName, 'public');
                
                // Eliminar documento anterior si existe
                if ($historyItem->document_path) {
                    \Storage::disk('public')->delete($historyItem->document_path);
                }
                
                $updateData['document_path'] = $documentPath;
            }

            // Agregar timestamp de actualización
            $updateData['updated_at'] = now();

            // Actualizar el registro de historial
            $updateResult = DB::table('fabricasoft_phase_info_history')
                ->where('id', $request->history_id)
                ->where('project_id', $projectId)
                ->update($updateData);

            if ($updateResult === 0) {
                return response()->json(['success' => false, 'message' => 'No se pudo actualizar el registro'], 500);
            }

            \Log::info('Información de la fase actualizada exitosamente por analista', [
                'project_id' => $projectId,
                'phase_id' => $historyItem->phase_id,
                'history_id' => $request->history_id,
                'updated_by' => $analystId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Información de la fase actualizada exitosamente.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error actualizando información de la fase por analista', [
                'project_id' => $projectId,
                'history_id' => $request->history_id ?? 'no proporcionado',
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar la información de la fase: ' . $e->getMessage()
            ], 500);
        }
    }
}
