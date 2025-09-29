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
        Schema::create('processus_transformations', function (Blueprint $table) {
             $table->id();
            $table->foreignId('proposition_transformation_id')->constrained('proposition_transformations')->onDelete('cascade');
            // If your Post_Déchet table is named postdechets, change the constrained() call accordingly.
            $table->foreignId('dechet_entrant_id')->constrained('post_dechets')->onDelete('cascade');
            $table->integer('duree_estimee')->nullable(); // in days or choose your unit
            $table->decimal('cout', 10, 2)->nullable();
            $table->text('equipements')->nullable();
            $table->enum('statut', ['en_cours','termine','annule'])->default('en_cours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processus_transformations');
    }
};
