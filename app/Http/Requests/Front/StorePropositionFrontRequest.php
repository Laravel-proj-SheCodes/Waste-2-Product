<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class StorePropositionFrontRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'description' => 'required|string|min:5|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Veuillez écrire votre proposition.',
            'description.min'      => 'Votre message est trop court.',
        ];
    }
}
