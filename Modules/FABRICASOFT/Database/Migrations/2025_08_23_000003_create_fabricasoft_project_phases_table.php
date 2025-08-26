<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fabricasoft_project_phases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('phase_name');
            $table->text('description');
            $table->enum('type', ['sprint', 'milestone', 'deliverable'])->default('sprint');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'blocked'])->default('planned');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->text('acceptance_criteria')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable(); // Miembro del equipo asignado
            $table->timestamps();
            
            $table->foreign('project_id')->references('id')->on('fabricasoft_projects')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'type']);
            $table->index('assigned_to');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fabricasoft_project_phases');
    }
};
