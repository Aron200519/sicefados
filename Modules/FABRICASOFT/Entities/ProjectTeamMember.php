<?php

namespace Modules\FABRICASOFT\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTeamMember extends Model
{
    protected $table = 'fabricasoft_project_team_members';
    
    protected $fillable = [
        'project_id',
        'user_id',
        'role',
        'status',
        'joined_date',
        'left_date',
        'responsibilities',
        'notes',
    ];

    protected $casts = [
        'joined_date' => 'date',
        'left_date' => 'date',
    ];

    /**
     * Relación con el proyecto
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación con el usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Verificar si el miembro está activo
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Verificar si el miembro es desarrollador
     */
    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    /**
     * Verificar si el miembro es tester
     */
    public function isTester(): bool
    {
        return $this->role === 'tester';
    }

    /**
     * Verificar si el miembro es diseñador
     */
    public function isDesigner(): bool
    {
        return $this->role === 'designer';
    }

    /**
     * Verificar si el miembro es analista de negocio
     */
    public function isBusinessAnalyst(): bool
    {
        return $this->role === 'business_analyst';
    }

    /**
     * Obtener el rol en español
     */
    public function getRoleTextAttribute(): string
    {
        return match($this->role) {
            'developer' => 'Desarrollador',
            'tester' => 'Tester',
            'designer' => 'Diseñador',
            'business_analyst' => 'Analista de Negocio',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener el estado en español
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'removed' => 'Removido',
            default => 'Desconocido'
        };
    }
}
