<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fabricasoft_project_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['developer', 'tester', 'designer', 'business_analyst'])->default('developer');
            $table->enum('status', ['active', 'inactive', 'removed'])->default('active');
            $table->date('joined_date');
            $table->date('left_date')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('project_id')->references('id')->on('fabricasoft_projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['project_id', 'user_id']);
            $table->index(['project_id', 'role']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fabricasoft_project_team_members');
    }
};
