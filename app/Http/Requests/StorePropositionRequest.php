<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropositionRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Autoriser la requête (tu pourras sécuriser plus tard avec des rôles si besoin)
        return true;
    }

    /**
     * Règles de validation pour stocker une proposition.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_dechet_id' => 'required|exists:post_dechets,id',
            'description'    => 'required|string|max:1000',
            'statut'         => 'nullable|in:en_attente,acceptée,rejetée',
            'prix'           => 'nullable|numeric|min:0',
            'user_id'        => 'required|exists:users,id',
        ];
    }
}
