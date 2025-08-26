<?php

namespace Modules\FABRICASOFT\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPhase extends Model
{
    protected $table = 'fabricasoft_project_phases';
    
    protected $fillable = [
        'project_id',
        'phase_name',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
        'duration_days',
        'acceptance_criteria',
        'notes',
        'assigned_to',
        'external_link',
        'document_path',
        'order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relación con el proyecto
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación con el usuario asignado
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    /**
     * Verificar si la fase está planificada
     */
    public function isPlanned(): bool
    {
        return $this->status === 'planned';
    }

    /**
     * Verificar si la fase está en progreso
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Verificar si la fase está completada
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Verificar si la fase está bloqueada
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Verificar si es un sprint
     */
    public function isSprint(): bool
    {
        return $this->type === 'sprint';
    }

    /**
     * Verificar si es un milestone
     */
    public function isMilestone(): bool
    {
        return $this->type === 'milestone';
    }

    /**
     * Verificar si es un deliverable
     */
    public function isDeliverable(): bool
    {
        return $this->type === 'deliverable';
    }

    /**
     * Obtener el tipo en español
     */
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'sprint' => 'Sprint',
            'milestone' => 'Hito',
            'deliverable' => 'Entregable',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener el estado en español
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'planned' => 'Planificado',
            'in_progress' => 'En Progreso',
            'completed' => 'Completado',
            'blocked' => 'Bloqueado',
            default => 'Desconocido'
        };
    }

    /**
     * Calcular la duración en días si no está establecida
     */
    public function getDurationDaysAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }

        return null;
    }
}
