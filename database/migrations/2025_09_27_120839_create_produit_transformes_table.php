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
        Schema::create('produit_transformes', function (Blueprint $table) {
               $table->id();
            $table->foreignId('processus_id')->constrained('processus_transformations')->onDelete('cascade');
            $table->string('nom_produit');
            $table->text('description')->nullable();
            $table->decimal('quantite_produite', 10, 2)->default(0);
            $table->decimal('valeur_ajoutee', 10, 2)->nullable();
            $table->decimal('prix_vente', 10, 2)->nullable();
            $table->string('photo')->nullable(); // store path if you want images
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produit_transformes');
    }
};
