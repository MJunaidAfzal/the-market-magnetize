<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();

            $table->json('delivery_files')->nullable();
            $table->text('delivery_note')->nullable();
            $table->unsignedBigInteger('status')->default(1)->comment('1: Pending, 2: Approved, 3: Rejected');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('order_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
