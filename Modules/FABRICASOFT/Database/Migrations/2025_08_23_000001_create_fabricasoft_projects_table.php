<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fabricasoft_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preregistration_id');
            $table->string('project_name');
            $table->text('description');
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->unsignedBigInteger('scrum_master_id'); // Líder del grupo (analista)
            $table->date('start_date')->nullable();
            $table->date('estimated_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->text('project_goals')->nullable();
            $table->text('success_criteria')->nullable();
            $table->timestamps();
            
            $table->foreign('preregistration_id')->references('id')->on('fabricasoft_preregistrations')->onDelete('cascade');
            $table->foreign('scrum_master_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['status', 'scrum_master_id']);
            $table->index('preregistration_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fabricasoft_projects');
    }
};
