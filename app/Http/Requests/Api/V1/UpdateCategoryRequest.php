<?php

namespace App\Http\Requests\Api\V1;

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
            'admin_id'=>'exists:admins,admin_id',
            'name' => 'string',
            'name_ar' =>'string',
            'description'=>'string',
            'description_ar' =>'string',
            'status' =>'in:active,inactive,archived',
        ];
    }
}
