<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropositionRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autoriser toutes les requêtes (tu peux mettre des conditions si besoin plus tard)
        return true;
    }

    /**
     * Règles de validation pour mettre à jour une Proposition.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description'   => 'sometimes|required|string|max:2000',
            'statut'        => 'sometimes|required|in:en_attente,accepte,refuse',
            'post_dechet_id'=> 'sometimes|exists:post_dechets,id',
            'user_id'       => 'sometimes|exists:users,id',
        ];
    }
}
