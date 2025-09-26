<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePropositionIdFromOffreTrocs extends Migration
{
    public function up()
    {
        Schema::table('offre_trocs', function (Blueprint $table) {
            // Supprimer la contrainte étrangère si elle existe
            $table->dropForeign(['proposition_id']);
            // Rendre la colonne nullable ou la supprimer
            $table->dropColumn('proposition_id');
        });
    }

    public function down()
    {
        Schema::table('offre_trocs', function (Blueprint $table) {
            $table->unsignedBigInteger('proposition_id')->nullable();
            $table->foreign('proposition_id')->references('id')->on('propositions')->onDelete('cascade');
        });
    }
}