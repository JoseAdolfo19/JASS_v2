<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associates', function (Blueprint $table) {
            $table->string('meter_number')->nullable()->after('address');
            $table->string('address_reference')->nullable()->after('meter_number');
            $table->dropColumn('status');
        });

        Schema::table('associates', function (Blueprint $table) {
            $table->enum('status', ['activo', 'suspendido'])->default('activo')->after('address_reference');
        });
    }

    public function down(): void
    {
        Schema::table('associates', function (Blueprint $table) {
            $table->dropColumn(['meter_number', 'address_reference', 'status']);
        });

        Schema::table('associates', function (Blueprint $table) {
            $table->boolean('status')->default(true);
        });
    }
};