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
        Schema::create('proposition_transformations', function (Blueprint $table) {
             $table->id();
            $table->foreignId('proposition_id')->constrained('propositions')->onDelete('cascade');
            $table->foreignId('transformateur_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->enum('statut', ['en_attente','accepté','refusé'])->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposition_transformations');
    }
};
