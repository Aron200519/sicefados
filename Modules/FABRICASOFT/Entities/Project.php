<?php

namespace Modules\FABRICASOFT\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $table = 'fabricasoft_projects';
    
    protected $fillable = [
        'preregistration_id',
        'project_name',
        'description',
        'status',
        'scrum_master_id',
        'start_date',
        'estimated_end_date',
        'actual_end_date',
        'project_goals',
        'success_criteria',
    ];

    protected $casts = [
        'start_date' => 'date',
        'estimated_end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    /**
     * Relación con la solicitud de preregistro
     */
    public function preregistration(): BelongsTo
    {
        return $this->belongsTo(Preregistration::class);
    }

    /**
     * Alias para la relación de preregistro (request)
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Preregistration::class, 'preregistration_id');
    }

    /**
     * Relación con la persona (cliente) a través del preregistration
     */
    public function people()
    {
        return $this->hasOneThrough(
            \Modules\SICA\Entities\Person::class,
            Preregistration::class,
            'id', // Clave foránea en preregistrations
            'id', // Clave foránea en people
            'preregistration_id', // Clave local en projects
            'person_id' // Clave local en preregistrations
        );
    }

    /**
     * Obtener el nombre del cliente (prioridad: preregistration.full_name, luego person, luego user)
     */
    public function getClientNameAttribute()
    {
        if ($this->preregistration && $this->preregistration->full_name) {
            return $this->preregistration->full_name;
        }
        
        if ($this->people) {
            return trim($this->people->first_name . ' ' . $this->people->first_last_name . ' ' . ($this->people->second_last_name ?? ''));
        }
        
        return 'Cliente no disponible';
    }

    /**
     * Relación con el Scrum Master (analista)
     */
    public function scrumMaster(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'scrum_master_id');
    }

    /**
     * Relación con los miembros del equipo
     */
    public function teamMembers(): HasMany
    {
        return $this->hasMany(ProjectTeamMember::class);
    }

    /**
     * Relación con las fases del proyecto
     */
    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class);
    }

    /**
     * Obtener desarrolladores del equipo
     */
    public function developers()
    {
        return $this->teamMembers()->where('role', 'developer')->where('status', 'active');
    }

    /**
     * Obtener testers del equipo
     */
    public function testers()
    {
        return $this->teamMembers()->where('role', 'tester')->where('status', 'active');
    }

    /**
     * Obtener diseñadores del equipo
     */
    public function designers()
    {
        return $this->teamMembers()->where('role', 'designer')->where('status', 'active');
    }

    /**
     * Obtener analistas de negocio del equipo
     */
    public function businessAnalysts()
    {
        return $this->teamMembers()->where('role', 'business_analyst')->where('status', 'active');
    }

    /**
     * Verificar si el proyecto está activo
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si el proyecto está en planificación
     */
    public function isPlanning(): bool
    {
        return $this->status === 'planning';
    }

    /**
     * Verificar si el proyecto está completado
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Obtener el estado del proyecto en español
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'planning' => 'Planificación',
            'active' => 'Activo',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado',
            default => 'Desconocido'
        };
    }
}
