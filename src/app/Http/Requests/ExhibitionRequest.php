<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // middlewareで認証済み想定
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'mimes:jpeg,png'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],

            'condition' => ['required', 'string'],
            'item_name' => ['required', 'string'],
            'brand_name' => ['nullable', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'regex:/^\d{1,3}(,\d{3})*$/', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => '商品画像をアップロードしてください',
            'image.mimes' => '商品画像は.jpeg または .png のみアップロードできます',
            'category_ids.required' => 'カテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'item_name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'price.required' => '商品の価格を入力してください',
            'price.numeric' => '価格は数値で入力してください',
            'price.min' =>  '価格は半角数字で入力してください（例：50000 または 50,000）'
        ];
    }
}
