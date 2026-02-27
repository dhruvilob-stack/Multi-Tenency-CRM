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
        if (! Schema::hasColumn('organizations', 'type')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->string('type')->default('manufacturer')->after('slug');
            });
        }

        if (! Schema::hasColumn('organizations', 'gst_number')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->string('gst_number')->nullable()->after('type');
            });
        }

        if (! Schema::hasColumn('organizations', 'address')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->string('address')->nullable()->after('gst_number');
            });
        }

        if (! Schema::hasColumn('organizations', 'contact_person')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->string('contact_person')->nullable()->after('address');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('organizations', 'contact_person')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->dropColumn('contact_person');
            });
        }

        if (Schema::hasColumn('organizations', 'address')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->dropColumn('address');
            });
        }

        if (Schema::hasColumn('organizations', 'gst_number')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->dropColumn('gst_number');
            });
        }

        if (Schema::hasColumn('organizations', 'type')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->dropColumn('type');
            });
        }
    }
};
