<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('donation_requests', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('donation_id');
            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donation_requests');
    }
}