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
       // database/migrations/xxxx_create_annonce_marketplaces_table.php
// database/migrations/xxxx_create_annonce_marketplaces_table.php
Schema::create('annonce_marketplaces', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_dechet_id')->constrained('post_dechets')->onDelete('cascade');
    $table->decimal('prix', 10, 2);
    $table->enum('statut_annonce', ['active', 'inactive', 'vendue', 'expiree']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annonce_marketplaces');
    }
};
