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
            $table->string('nip')->nullable()->after('email');
            $table->uuid('jabatan_id')->nullable()->after('nip');
            $table->uuid('lokasi_id')->nullable()->after('jabatan_id');

            // Foreign key constraints
            $table->foreign('jabatan_id')->references('id')->on('jabatan')->onDelete('set null');
            $table->foreign('lokasi_id')->references('id')->on('lokasi')->onDelete('set null');

            // Indexes for better query performance
            $table->index('nip');
            $table->index('jabatan_id');
            $table->index('lokasi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropForeign(['lokasi_id']);
            $table->dropIndex(['nip']);
            $table->dropIndex(['jabatan_id']);
            $table->dropIndex(['lokasi_id']);
            $table->dropColumn(['nip', 'jabatan_id', 'lokasi_id']);
        });
    }
};
