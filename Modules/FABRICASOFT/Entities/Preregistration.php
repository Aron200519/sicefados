<?php

namespace Modules\FABRICASOFT\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Modules\SICA\Entities\Person;

class Preregistration extends Model
{
    use HasFactory;

    protected $table = 'fabricasoft_preregistrations';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'organization',
        'password',
        'software_type',
        'project_description',
        'additional_requirements',
        'client_type',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'assigned_analyst_id',
        'assigned_at',
        'analysis_status',
        'srs_file_path',
        'srs_requirements',
        'analyst_notes',
        'srs_uploaded_at',
        'workflow_status',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'assigned_at' => 'datetime',
        'srs_uploaded_at' => 'datetime',
    ];

    /**
     * Relación con el usuario que revisó la solicitud
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Relación con el analista asignado
     */
    public function analyst()
    {
        return $this->belongsTo(User::class, 'assigned_analyst_id');
    }

    /**
     * Relación con la persona del SICA (si existe)
     */
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    /**
     * Scope para filtrar por tipo de software
     */
    public function scopeBySoftwareType($query, $softwareType)
    {
        return $query->where('software_type', $softwareType);
    }

    /**
     * Scope para filtrar por estado del flujo de trabajo
     */
    public function scopeByWorkflowStatus($query, $workflowStatus)
    {
        return $query->where('workflow_status', $workflowStatus);
    }

    /**
     * Scope para filtrar por estado de análisis
     */
    public function scopeByAnalysisStatus($query, $analysisStatus)
    {
        return $query->where('analysis_status', $analysisStatus);
    }

    /**
     * Scope para solicitudes asignadas a un analista específico
     */
    public function scopeAssignedToAnalyst($query, $analystId)
    {
        return $query->where('assigned_analyst_id', $analystId);
    }

    /**
     * Verificar si la solicitud está lista para revisión del admin
     */
    public function isReadyForAdminReview()
    {
        return $this->workflow_status === 'srs_ready';
    }

    /**
     * Verificar si la solicitud puede ser asignada a un analista
     */
    public function canBeAssignedToAnalyst()
    {
        return $this->workflow_status === 'pending';
    }

    /**
     * Verificar si la solicitud está en análisis
     */
    public function isUnderAnalysis()
    {
        return in_array($this->workflow_status, ['assigned', 'analysis']);
    }

    /**
     * Obtener el estado del flujo de trabajo en español
     */
    public function getWorkflowStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Pendiente de Asignar',
            'assigned' => 'Asignada a Analista',
            'analysis' => 'En Análisis',
            'srs_ready' => 'SRS Listo',
            'admin_review' => 'En Revisión Admin',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada'
        ];

        return $statuses[$this->workflow_status] ?? $this->workflow_status;
    }

    /**
     * Obtener el estado de análisis en español
     */
    public function getAnalysisStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Pendiente',
            'in_progress' => 'En Progreso',
            'completed' => 'Completado'
        ];

        return $statuses[$this->analysis_status] ?? $this->analysis_status;
    }
}
