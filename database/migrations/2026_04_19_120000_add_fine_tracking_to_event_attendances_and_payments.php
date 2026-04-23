<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('event_attendances', 'fine_paid')) {
                $table->boolean('fine_paid')->default(false)->after('status');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'fine_amount')) {
                $table->decimal('fine_amount', 10, 2)->default(0)->after('late_fee_applied');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_attendances', function (Blueprint $table) {
            if (Schema::hasColumn('event_attendances', 'fine_paid')) {
                $table->dropColumn('fine_paid');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'fine_amount')) {
                $table->dropColumn('fine_amount');
            }
        });
    }
};
