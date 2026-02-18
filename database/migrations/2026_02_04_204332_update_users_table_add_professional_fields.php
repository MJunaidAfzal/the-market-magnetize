<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('username')->nullable()->unique()->after('name');

            $table->string('phone_number')->nullable()->after('email');

            $table->string('profile_photo')->nullable()->after('phone_number');

            $table->date('date_of_birth')->nullable()->after('profile_photo');

            $table->enum('status', ['active', 'inactive', 'suspended'])
                  ->default('active')
                  ->after('date_of_birth');

            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'username',
                'phone_number',
                'profile_photo',
                'date_of_birth',
                'status',
                'last_login_at'
            ]);
        });
    }
};
