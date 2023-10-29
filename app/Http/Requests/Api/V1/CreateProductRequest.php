<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'provider_id' => 'required|exists:providers,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string',
            'title_ar' => 'required|string',
            'description' => 'required|string',
            'description_ar' => 'required|string',
            'price' => 'required|numeric',
            // 'discount' => 'nullable|numeric',
        ];
    }
}
