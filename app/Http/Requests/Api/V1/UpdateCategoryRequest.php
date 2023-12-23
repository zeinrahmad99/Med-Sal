<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'admin_id' => ['exists:admins,admin_id'],
            'name' => ['string', 'min:0', 'max:50', Rule::unique('categories', 'name')->ignore($this->route('id'))],
            'name_ar' => ['string', 'min:0', 'max:50', Rule::unique('categories', 'name')->ignore($this->route('id'))],
            'description' => ['string', 'min:0', 'max:500'],
            'description_ar' => ['string', 'min:0', 'max:500'],
            'status' => ['in:active,inactive,archived'],
        ];
    }
}
