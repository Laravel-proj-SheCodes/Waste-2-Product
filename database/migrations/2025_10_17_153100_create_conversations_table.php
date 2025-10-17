<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('conversations', function (Blueprint $t) {
      $t->id();
      $t->foreignId('post_dechet_id')->constrained('post_dechets')->cascadeOnDelete();
      $t->foreignId('proposition_id')->constrained('propositions')->cascadeOnDelete();
      $t->foreignId('owner_id')->constrained('users')->cascadeOnDelete();   // propriétaire du post
      $t->foreignId('client_id')->constrained('users')->cascadeOnDelete();  // auteur de la proposition
      $t->enum('status', ['active','closed'])->default('active');
      $t->timestamps();

      $t->unique(['proposition_id']); // 1 conversation par proposition acceptée
      $t->index(['owner_id','client_id']);
    });
  }
  public function down(): void {
    Schema::dropIfExists('conversations');
  }
};
