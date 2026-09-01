<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * カテゴリの更新権限を判定する。
     * 詳しい理由は CategoryPolicy::update() のコメントを参照。
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('category'));
    }

    /**
     * リクエストに適用するバリデーションルールを取得する。
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'カテゴリ名',
            'type' => '種別',
        ];
    }
}
