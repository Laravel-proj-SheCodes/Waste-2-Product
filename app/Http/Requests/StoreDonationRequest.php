<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'location' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:10000',
            'type' => 'required|in:recyclable,renewable',
            'donation_date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string|max:30000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'location.required' => 'The location field is required.',
            'location.max' => 'The location must not exceed 255 characters.',
            'product_name.required' => 'The product name is required.',
            'product_name.max' => 'The product name must not exceed 255 characters.',
            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be a valid number.',
            'quantity.min' => 'The quantity must be at least 1.',
            'quantity.max' => 'The quantity must not exceed 10,000.',
            'type.required' => 'The donation type is required.',
            'type.in' => 'The donation type must be either recyclable or renewable.',
            'donation_date.required' => 'The donation date is required.',
            'donation_date.date' => 'The donation date must be a valid date.',
            'donation_date.after_or_equal' => 'The donation date cannot be in the past.',
            'description.max' => 'The description must not exceed 30000 characters.',
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