<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOffreTrocsTableAddFields extends Migration
{
    public function up()
    {
        Schema::table('offre_trocs', function (Blueprint $table) {
            // Ajouter les colonnes manquantes uniquement
            if (!Schema::hasColumn('offre_trocs', 'categorie')) {
                $table->string('categorie')->nullable()->after('id');
            }
            if (!Schema::hasColumn('offre_trocs', 'quantite')) {
                $table->integer('quantite')->nullable()->after('categorie');
            }
            if (!Schema::hasColumn('offre_trocs', 'unite_mesure')) {
                $table->string('unite_mesure')->nullable()->after('quantite');
            }
            if (!Schema::hasColumn('offre_trocs', 'etat')) {
                $table->string('etat')->nullable()->after('unite_mesure');
            }
            if (!Schema::hasColumn('offre_trocs', 'localisation')) {
                $table->string('localisation')->nullable()->after('etat');
            }
            if (!Schema::hasColumn('offre_trocs', 'photos')) {
                $table->json('photos')->nullable()->after('localisation');
            }
            if (!Schema::hasColumn('offre_trocs', 'description')) {
                $table->text('description')->nullable()->after('photos');
            }
            if (!Schema::hasColumn('offre_trocs', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable()->after('description');
            }
            if (!Schema::hasColumn('offre_trocs', 'post_dechet_id')) {
                $table->foreignId('post_dechet_id')->constrained('post_dechets')->onDelete('cascade')->nullable()->after('user_id');
            }

            // Ajouter la colonne status si elle n'existe pas
            if (Schema::hasColumn('offre_trocs', 'statut') && !Schema::hasColumn('offre_trocs', 'status')) {
                $table->enum('status', ['accepted', 'rejected', 'en_attente'])->default('en_attente')->after('post_dechet_id');
            }
        });

        // Mettre à jour les données après l'ajout de la colonne
        if (Schema::hasColumn('offre_trocs', 'statut') && Schema::hasColumn('offre_trocs', 'status')) {
            \DB::statement("UPDATE offre_trocs SET status = CASE 
                WHEN statut = 'en_attente' THEN 'en_attente'
                WHEN statut = 'accepté' THEN 'accepted'
                WHEN statut = 'rejeté' THEN 'rejected'
                ELSE 'en_attente' END WHERE statut IS NOT NULL");
            Schema::table('offre_trocs', function (Blueprint $table) {
                $table->dropColumn('statut');
            });
        }
    }

    public function down()
    {
        Schema::table('offre_trocs', function (Blueprint $table) {
            $table->dropColumn(['categorie', 'quantite', 'unite_mesure', 'etat', 'localisation', 'photos', 'description', 'user_id', 'post_dechet_id', 'status']);
            if (!Schema::hasColumn('offre_trocs', 'statut')) {
                $table->string('statut')->nullable();
            }
            if (!Schema::hasColumn('offre_trocs', 'proposition_id')) {
                $table->string('proposition_id')->nullable();
            }
            if (!Schema::hasColumn('offre_trocs', 'dechet_propose_id')) {
                $table->string('dechet_propose_id')->nullable();
            }
            if (!Schema::hasColumn('offre_trocs', 'conditions_echange')) {
                $table->string('conditions_echange')->nullable();
            }
        });
    }
}