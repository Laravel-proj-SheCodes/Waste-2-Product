<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropositionTransformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proposition_id'     => 'required|exists:propositions,id',
            'transformateur_id'  => 'required|exists:users,id',
            'description'        => 'nullable|string|max:2000',
            'statut'             => 'nullable|in:en_attente,accepté,refusé',
        ];
    }
}
