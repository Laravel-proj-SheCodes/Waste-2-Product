<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // important
    }

    public function rules(): array
    {
        return [
            'post_dechet_id'   => ['required','exists:post_dechets,id'],
            'user_id'          => ['required','exists:users,id'],
            'description'      => ['required','string','max:2000'],
            'date_proposition' => ['required','date'],
            'statut'           => ['required','in:en_attente,accepté,refusé'],
        ];
    }
}
