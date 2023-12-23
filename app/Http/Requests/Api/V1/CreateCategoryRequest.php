<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
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
            'admin_id'=>'required|exists:admins,admin_id',
            'name' => 'required|string|min:0|max:50|unique:categories,name',
            'name_ar' =>'required|string|min:0|max:50|unique:categories,name',
            'description'=>'required|string|min:0|max:500',
            'description_ar' =>'required|string|min:0|max:500',
        ];
    }
}
