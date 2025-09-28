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
       // database/migrations/xxxx_create_commandes_table.php
// database/migrations/xxxx_create_commandes_table.php
Schema::create('commandes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('annonce_marketplace_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // acheteur
    $table->integer('quantite');
    $table->decimal('prix_total', 10, 2);
    $table->enum('statut_commande', ['en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree', 'annulee']);
    $table->timestamp('date_commande');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
