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
        Schema::create('offre_trocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposition_id')->constrained('propositions')->onDelete('cascade');
            $table->foreignId('dechet_propose_id')->nullable()->constrained('post_dechets')->onDelete('set null'); // Ou 'produit_transformes' si séparé
            $table->string('conditions_echange');
            $table->enum('statut', ['en_attente', 'accepte', 'refuse', 'annule'])->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offre_trocs');
    }
};
