<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduitTransformeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'processus_id'       => 'required|exists:processus_transformations,id',
            'nom_produit'        => 'required|string|max:255',
            'description'        => 'nullable|string|max:2000',
            'quantite_produite'  => 'required|integer|min:1',
            'valeur_ajoutee'     => 'required|numeric|min:0',
            'prix_vente'         => 'nullable|numeric|min:0',
        ];
    }
}
