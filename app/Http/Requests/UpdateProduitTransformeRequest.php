<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProduitTransformeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'processus_id'       => 'sometimes|required|exists:processus_transformations,id',
            'nom_produit'        => 'sometimes|required|string|max:255',
            'description'        => 'sometimes|nullable|string|max:2000',
            'quantité_produite'  => 'sometimes|required|integer|min:1',
            'valeur_ajoutée'     => 'sometimes|required|numeric|min:0',
            'prix_vente'         => 'nullable|numeric|min:0',
        ];
    }
}
