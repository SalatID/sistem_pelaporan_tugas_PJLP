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
        Schema::table('tugas', function (Blueprint $table) {
            // Drop the old foto column if it exists
            if (Schema::hasColumn('tugas', 'foto')) {
                $table->dropColumn('foto');
            }

            // Add three new photo columns
            $table->string('foto_sebelum')->nullable()->after('deskripsi')->comment('Foto sebelum pengerjaan');
            $table->string('foto_pengerjaan')->nullable()->after('foto_sebelum')->comment('Foto saat pengerjaan');
            $table->string('foto_sesudah')->nullable()->after('foto_pengerjaan')->comment('Foto setelah pengerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            if (Schema::hasColumn('tugas', 'foto_sebelum')) {
                $table->dropColumn(['foto_sebelum', 'foto_pengerjaan', 'foto_sesudah']);
            }

            // Restore the old foto column
            $table->string('foto')->nullable();
        });
    }
};
