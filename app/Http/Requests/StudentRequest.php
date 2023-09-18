<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:225',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '生徒名',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => ':attributeを入力してください。',
            'name.max' => ':attributeは:max文字以内で入力してください。',
        ];
    }
}
