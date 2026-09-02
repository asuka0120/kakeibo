<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * 収支記録の更新権限を判定する。
     * 詳しい理由は TransactionPolicy::update() のコメントを参照。
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('transaction'));
    }

    /**
     * リクエストに適用するバリデーションルールを取得する。
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(
                    fn ($query) => $query->where('user_id', $this->user()->id)
                ),
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'memo' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'date' => '日付',
            'type' => '種別',
            'category_id' => 'カテゴリ',
            'amount' => '金額',
            'memo' => 'メモ',
        ];
    }

    /**
     * 編集時にも、選択したカテゴリの種別（収入用/支出用）と、収支記録自体の種別（収入/支出）が一致するか確認する。
     * 新規登録時は正しくても、編集でどちらか一方だけを変更してしまうと不整合になり、収入・支出の集計が実際の家計とズレてしまうため。
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('category_id') || $validator->errors()->has('type')) {
                return;
            }

            $category = Category::find($this->input('category_id'));

            if ($category && $category->type !== $this->input('type')) {
                $validator->errors()->add('category_id', '選択したカテゴリの種別と一致しません。');
            }
        });
    }
}
