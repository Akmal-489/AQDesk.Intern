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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->default(1)->after('id')->constrained('roles', 'role_id');
            $table->string('auth_provider', 20)->default('email')->after('password');
            $table->string('profile_photo')->nullable()->after('auth_provider');
            $table->text('bio')->nullable()->after('profile_photo');
            $table->enum('status', ['active', 'suspended'])->default('active')->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'auth_provider', 'profile_photo', 'bio', 'status']);
        });
    }
};
