<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description'      => ['sometimes','required','string','max:2000'],
            'date_proposition' => ['sometimes','required','date'],
            'statut'           => ['sometimes','required','in:en_attente,accepté,refusé'],
        ];
    }
}
