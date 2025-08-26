<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fabricasoft_preregistrations', function (Blueprint $table) {
            // Campos para el analista
            $table->unsignedBigInteger('assigned_analyst_id')->nullable()->after('reviewed_by');
            $table->timestamp('assigned_at')->nullable()->after('assigned_analyst_id');
            $table->enum('analysis_status', ['pending', 'in_progress', 'completed'])->default('pending')->after('assigned_at');
            
            // Campos para el SRS
            $table->string('srs_file_path')->nullable()->after('analysis_status');
            $table->text('srs_requirements')->nullable()->after('srs_file_path');
            $table->text('analyst_notes')->nullable()->after('srs_requirements');
            $table->timestamp('srs_uploaded_at')->nullable()->after('analyst_notes');
            
            // Campos para el flujo de trabajo
            $table->enum('workflow_status', [
                'pending',           // Pendiente de asignar analista
                'assigned',          // Asignada a analista
                'analysis',          // En análisis
                'srs_ready',         // SRS listo para revisión
                'admin_review',      // En revisión del admin
                'approved',          // Aprobada
                'rejected'           // Rechazada
            ])->default('pending')->after('srs_uploaded_at');
            
            // Índices para mejorar el rendimiento
            $table->index(['assigned_analyst_id', 'analysis_status'], 'idx_analyst_analysis');
            $table->index(['workflow_status', 'analysis_status'], 'idx_workflow_analysis');
            $table->index('assigned_at', 'idx_assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fabricasoft_preregistrations', function (Blueprint $table) {
            $table->dropIndex('idx_analyst_analysis');
            $table->dropIndex('idx_workflow_analysis');
            $table->dropIndex('idx_assigned_at');
            
            $table->dropColumn([
                'assigned_analyst_id',
                'assigned_at',
                'analysis_status',
                'srs_file_path',
                'srs_requirements',
                'analyst_notes',
                'srs_uploaded_at',
                'workflow_status'
            ]);
        });
    }
};
