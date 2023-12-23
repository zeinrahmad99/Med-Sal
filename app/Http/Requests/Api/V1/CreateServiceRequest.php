<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'service_location_id' => 'required|exists:service_locations,id',
            'name' => 'required|string|min:0|max:50',
            'name_ar' => 'required|string|min:0|max:50',
            'description' => 'required|string|min:0|max:300',
            'description_ar' => 'required|string|min:0|max:300',
            'price' => 'required|numeric|min:0|max:10000000000000',
            'discount' => 'required|sometimes|numeric|min:0',
        ];
    }
}