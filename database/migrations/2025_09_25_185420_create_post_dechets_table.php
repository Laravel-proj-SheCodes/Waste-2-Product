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
        Schema::create('post_dechets', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->enum('type_post', ['don', 'troc', 'vente', 'transformation']);
            $table->string('categorie');
            $table->float('quantite');
            $table->string('unite_mesure');
            $table->string('etat'); // neuf, usagé, dégradé
            $table->string('localisation');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // FK vers users
            $table->date('date_publication')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'terminé'])->default('en_attente');
            $table->string('photos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_dechets');
    }
};
