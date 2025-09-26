<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostDechetRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autoriser pour l’instant tout le monde.
        // Plus tard tu pourras mettre une logique comme : return auth()->check();
        return true;
    }

    /**
     * Règles de validation appliquées à la requête.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre'        => 'required|string|max:255',
            'type_post'    => 'required|in:don,troc,vente,transformation',
            'categorie'    => 'required|string|max:255',
            'quantite'     => 'required|numeric|min:0.01',
            'unite_mesure' => 'required|string|max:50',
            'etat'         => 'required|in:neuf,usagé,dégradé',
            'localisation' => 'required|string|max:255',
            'description'  => 'required|string',
            'photos.*'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'statut'       => 'nullable|in:en_attente,en_cours,terminé',
        ];
    }
}
