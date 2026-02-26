<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_assign_to_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('assigned_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('unassigned_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['lead_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_assign_to_users');
    }
};
