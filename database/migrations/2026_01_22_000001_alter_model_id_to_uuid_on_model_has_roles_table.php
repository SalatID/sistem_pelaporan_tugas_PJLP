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
        Schema::table('model_has_roles', function (Blueprint $table) {
            
            // Change model_id column to uuid
            $table->uuid('model_id')->change();
            
            // Recreate foreign key if needed
            // $table->foreign('model_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_has_roles', function (Blueprint $table) {
            
            // Change back to unsignedBigInteger
            $table->unsignedBigInteger('model_id')->change();
            
            // Recreate foreign key if needed
            // $table->foreign('model_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
