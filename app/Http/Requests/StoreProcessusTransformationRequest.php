<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessusTransformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
public function rules(): array
{
    return [
        'proposition_transformation_id' => 'required|exists:proposition_transformations,id',
        'dechet_entrant_id'             => 'required|exists:post_dechets,id',
        'duree_estimee'                 => 'required|integer|min:1',
        'cout'                          => 'required|numeric|min:0',
        'equipements'                   => 'nullable|string|max:255',
        'statut'                        => 'nullable|in:en_cours,termine,annule',
    ];
}

}
