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
            $table->string('role')->default('organization_admin')->after('password');
            $table->foreignId('organization_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active']);
            $table->dropConstrainedForeignId('organization_id');
        });
    }
};
