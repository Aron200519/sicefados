<?php

namespace Modules\FABRICASOFT\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhaseInfoHistory extends Model
{
    protected $table = 'fabricasoft_phase_info_history';
    
    protected $fillable = [
        'project_id',
        'phase_id',
        'info_type',
        'content',
        'description',
        'notes',
        'external_link',
        'start_date',
        'end_date',
        'additional_comment',
        'document_path',
        'added_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el proyecto
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación con la fase
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class);
    }

    /**
     * Relación con el usuario que agregó la información
     */
    public function addedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'added_by');
    }

    /**
     * Obtener el tipo de información en formato legible
     */
    public function getInfoTypeTextAttribute(): string
    {
        return match($this->info_type) {
            'description' => 'Descripción',
            'notes' => 'Notas',
            'link' => 'Enlace',
            'document' => 'Documento',
            'comment' => 'Comentario',
            default => ucfirst($this->info_type)
        };
    }

    /**
     * Obtener el icono correspondiente al tipo de información
     */
    public function getInfoTypeIconAttribute(): string
    {
        return match($this->info_type) {
            'description' => 'fas fa-info-circle',
            'notes' => 'fas fa-sticky-note',
            'link' => 'fas fa-external-link-alt',
            'document' => 'fas fa-file-download',
            'comment' => 'fas fa-comment',
            default => 'fas fa-info'
        };
    }
}
