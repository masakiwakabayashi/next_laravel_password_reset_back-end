<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendPasswordResetEmailRequest extends FormRequest
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
            // filterとdnsをつけることで、ひらがなやカタカナや漢字が含まれているものと存在しないドメインのメールアドレスは弾かれる
            'email' => 'required|email:filter,dns',
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'メールアドレス',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => ':attributeを入力してください。',
            'email.email' => ':attributeを入力してください。',
        ];
    }
}
