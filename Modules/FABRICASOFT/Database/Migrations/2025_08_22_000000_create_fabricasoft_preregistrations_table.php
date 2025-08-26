<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fabricasoft_preregistrations', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('organization');
            $table->string('software_type');
            $table->text('project_description');
            $table->text('additional_requirements')->nullable();
            $table->enum('client_type', ['cliente_interno', 'cliente_externo'])->default('cliente_externo');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index(['email', 'status']);
            $table->index(['client_type', 'status']);
            $table->index(['software_type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabricasoft_preregistrations');
    }
};
