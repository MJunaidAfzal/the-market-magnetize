<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();

            // Source Relation
            $table->foreignId('lead_source_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // Status & Pipeline
            $table->enum('status', [
                'new',
                'contacted',
                'qualified',
                'won',
                'lost'
            ])->default('new')->index();

            $table->decimal('value', 12, 2)->nullable(); // potential deal value
            $table->integer('score')->default(0); // lead scoring

            // Address Info
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();

            // Extra Data
            $table->text('notes')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('follow_up_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
