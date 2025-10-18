<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'location' => 'sometimes|string|max:255',
            'product_name' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer|min:1|max:10000',
            'type' => 'sometimes|in:recyclable,renewable',
            'donation_date' => 'sometimes|date',
            'description' => 'nullable|string|max:30000',
            'status' => 'sometimes|in:pending,accepted,rejected,taken',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'location.max' => 'The location must not exceed 255 characters.',
            'product_name.max' => 'The product name must not exceed 255 characters.',
            'quantity.integer' => 'The quantity must be a valid number.',
            'quantity.min' => 'The quantity must be at least 1.',
            'quantity.max' => 'The quantity must not exceed 10,000.',
            'type.in' => 'The donation type must be either recyclable or renewable.',
            'donation_date.date' => 'The donation date must be a valid date.',
            'description.max' => 'The description must not exceed 30000 characters.',
            'status.in' => 'The status must be one of: pending, accepted, rejected, or taken.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'product_name' => 'product name',
            'donation_date' => 'donation date',
        ];
    }
}