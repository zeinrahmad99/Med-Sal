<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterProviderRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users',
            'password' => [ 'required', 'confirmed', Password::min( 8 )->letters()->numbers() ],
            'contact_number' => [ 'required', 'regex:^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$^' ],
            'business_name' => 'required|string',
            'service_type_id' => 'required|exists:categories,id',
            'bank_name' => 'required|string',
            'iban' => 'required|string',
            'swift_code' => 'required|string',
            'document' => 'required|file|mimes:pdf|max:2048',
        ];

    }
}
