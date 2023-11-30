<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'provider_id' => 'exists:providers,id',
            'category_id' => 'exists:categories,id',
            'name' => 'string',
            'name_ar' => 'string',
            'description' => 'string',
            'description_ar' => 'string',
            'price' => 'numeric',
            'status' => 'in:active,inactive,pending,unaccept',
            'discount' => 'numeric|min:0',
            'quantity' => 'integer',
            'images' => ['array', 'min:1', 'max:2'],
            'images.*' => ['mimes:jpeg,jpg,png', 'max:2048'],

        ];
    }
}
