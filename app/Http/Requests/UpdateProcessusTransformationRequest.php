<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessusTransformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proposition_transformation_id' => 'sometimes|required|exists:proposition_transformations,id',
            'déchet_entrant_id' => 'sometimes|required|exists:post_dechets,id',

            'durée_estimée'                 => 'sometimes|required|integer|min:1',
            'coût'                          => 'sometimes|required|numeric|min:0',
            'équipements'                   => 'sometimes|nullable|string|max:255',
            'statut'                        => 'nullable|in:en_cours,terminé',
        ];
    }
}
