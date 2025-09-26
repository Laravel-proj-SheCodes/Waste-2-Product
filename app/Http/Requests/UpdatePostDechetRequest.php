<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostDechetRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autoriser toutes les requêtes (tu peux ajouter des conditions plus tard si besoin)
        return true;
    }

    /**
     * Règles de validation pour mettre à jour un Post Déchet.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre'        => 'sometimes|required|string|max:255',
            'type_post'    => 'sometimes|required|in:don,troc,vente,transformation',
            'categorie'    => 'sometimes|required|string|max:255',
            'quantite'     => 'sometimes|required|numeric|min:0.01',
            'unite_mesure' => 'sometimes|required|string|max:50',
            'etat'         => 'sometimes|required|in:neuf,usagé,dégradé',
            'localisation' => 'sometimes|required|string|max:255',
            'description'  => 'sometimes|required|string|max:2000',
            'photos.*'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2Mo par image
            'statut'       => 'nullable|in:en_attente,en_cours,terminé',
        ];
    }
}
