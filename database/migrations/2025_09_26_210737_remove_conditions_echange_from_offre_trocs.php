<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveConditionsEchangeFromOffreTrocs extends Migration
{
    public function up()
    {
        Schema::table('offre_trocs', function (Blueprint $table) {
            $table->dropColumn('conditions_echange');
        });
    }

    public function down()
    {
        Schema::table('offre_trocs', function (Blueprint $table) {
            $table->string('conditions_echange')->nullable();
        });
    }
}