<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // middlewareで認証済み想定
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $this->merge([
                'price' => str_replace(',', '', $this->price),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'mimes:jpeg,png'],

            // カテゴリは1つ以上必須
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],

            'condition' => ['required', 'string'],
            'item_name' => ['required', 'string'],
            'brand_name' => ['nullable', 'string'],
            'description' => ['required', 'string', 'max:255'],

            // prepareForValidation でカンマ除去後に整数判定
            'price' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => '商品画像をアップロードしてください',
            'image.file' => '商品画像を正しく選択してください',
            'image.mimes' => '商品画像は.jpeg または .png のみアップロードできます',

            'category_ids.required' => 'カテゴリーを選択してください',
            'category_ids.array' => 'カテゴリーを正しく選択してください',
            'category_ids.min' => 'カテゴリーを1つ以上選択してください',
            'category_ids.*.integer' => 'カテゴリーを正しく選択してください',
            'category_ids.*.exists' => '選択されたカテゴリーは存在しません',

            'condition.required' => '商品の状態を選択してください',
            'item_name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',

            'price.required' => '商品の価格を入力してください',
            'price.integer' => '価格は半角数字で入力してください（例：50000 または 50,000）',
            'price.min' => '価格は1円以上で入力してください',
        ];
    }
}