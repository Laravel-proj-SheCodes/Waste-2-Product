<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropositionTransformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proposition_id'     => 'sometimes|required|exists:propositions,id',
            'transformateur_id'  => 'sometimes|required|exists:users,id',
            'description'        => 'sometimes|nullable|string|max:2000',
            'statut'             => 'nullable|in:en_attente,accepté,refusé',
        ];
    }
}
