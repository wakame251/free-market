<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::in(['konbini', 'card'])],
            'post_code'      => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'        => ['required', 'string'],
            'building'       => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in'       => '支払い方法を正しく選択してください。',
            'post_code.required'      => '郵便番号を入力してください。',
            'post_code.regex'         => '郵便番号はハイフンありの8文字（例: 123-4567）で入力してください。',
            'address.required'        => '住所を入力してください。',
        ];
    }
}