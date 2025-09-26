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
        Schema::create('transaction_trocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offre_troc_id')->constrained('offre_trocs')->onDelete('cascade');
            $table->foreignId('utilisateur_acceptant_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('date_accord');
            $table->enum('statut_livraison', ['en_cours', 'livre', 'annule'])->default('en_cours');
            $table->text('evaluation_mutuelle')->nullable();
            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_trocs');
    }
};
