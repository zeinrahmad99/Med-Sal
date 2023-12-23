<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'email' => ['email', Rule::unique('users', 'email')->ignore($this->route('id'))],
            'password' => ['confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

}
