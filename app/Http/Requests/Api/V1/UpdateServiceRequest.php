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
            'name' => 'string|min:0|max:50',
            'name_ar' => 'string|min:0|max:50',
            'description' => 'string|min:0|max:300',
            'description_ar' => 'string|min:0|max:300',
            'time_requested' => 'string',
            'price' => 'numeric|min:0|max:10000000000000',
            'status' => 'in:active,inactive,pending,unaccept',
            'discount' => 'numeric|min:0',

        ];
    }
}
