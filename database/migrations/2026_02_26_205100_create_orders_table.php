<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('lead_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('order_category_id')
                  ->nullable()
                  ->constrained('order_categories')
                  ->nullOnDelete();

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Basic Info
            $table->string('order_number')->unique();
            $table->string('title');
            $table->longText('description')->nullable();

            // Workflow
            $table->enum('status', [
                'Pending',
                'Confirmed',
                'In Progress',
                'On Hold',
                'Completed',
                'Cancelled'
            ])->default('Pending');

            $table->enum('priority', [
                'Low',
                'Medium',
                'High',
                'Urgent'
            ])->default('Medium');

            // Financial
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);

            $table->enum('payment_status', [
                'Unpaid',
                'Partial',
                'Paid',
                'Overdue'
            ])->default('Unpaid');

            // Dates
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Attachments (Multiple Files)
            $table->json('attachments')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
