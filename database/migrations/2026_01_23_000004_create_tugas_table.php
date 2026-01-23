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
        Schema::create('tugas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->uuid('kategori_id');
            $table->uuid('pengguna_id');
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->uuid('created_user')->nullable();
            $table->uuid('updated_user')->nullable();

            // Foreign key constraints
            $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('cascade');
            $table->foreign('pengguna_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_user')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_user')->references('id')->on('users')->onDelete('set null');

            // Indexes for better query performance
            $table->index('kategori_id');
            $table->index('pengguna_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
