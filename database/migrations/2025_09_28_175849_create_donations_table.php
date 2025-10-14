<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->unsignedBigInteger('user_id'); // donor
            $table->string('location');
            $table->string('product_name');
            $table->integer('quantity');
            $table->enum('type', ['recyclable', 'renewable']);
            $table->text('description')->nullable();
            $table->date('donation_date');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'taken'])->default('pending');
            
            $table->unsignedBigInteger('taken_by_user_id')->nullable(); // who took the donation
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('taken_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
