<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 画像：jpeg/pngのみ（nullableにするなら更新時のみOK）
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png'],

            // ユーザー名：必須・20文字以内
            'user_name' => ['required', 'string', 'max:20'],

            // 郵便番号：必須・ハイフンあり8文字（例: 123-4567）
            'post_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],

            // 住所：必須
            'address' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            // avatar
            'avatar.image' => '画像ファイルを選択してください',
            'avatar.mimes' => '画像はjpegまたはpng形式でアップロードしてください',

            // user_name
            'user_name.required' => 'ユーザー名を入力してください',
            'user_name.max' => 'ユーザー名は20文字以内で入力してください',

            // post_code
            'post_code.required' => '郵便番号を入力してください',
            'post_code.regex' => '郵便番号は「123-4567」の形式で入力してください',

            // address
            'address.required' => '住所を入力してください',
        ];
    }
}