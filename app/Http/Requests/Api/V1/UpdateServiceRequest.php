<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'category_id' => 'exists:categories,id',
            'service_location_id' => 'exists:service_locations,id',
            'name' => 'string',
            'name_ar' => 'string',
            'description' => 'string',
            'description_ar' => 'string',
            'price' => 'numeric|min:0',
            'status' => 'in:active,inactive,pending,unaccept',
            'discount' => 'numeric|min:0',

        ];
    }
}
