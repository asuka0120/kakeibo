<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * ユーザーが一覧画面を開けるかどうかを判定する。
     * 誰でも一覧画面自体は開けるようにしているが、
     * 実際に表示されるデータは、モデル側のGlobal Scopeで自分のものだけに絞られているため、他人のデータが見える心配はない。
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * 特定の1件のカテゴリが、ログイン中のユーザー自身のものかを判定する。
     * Global Scopeで既に絞り込んでいるが、
     * 万が一その実装にバグがあった場合でも、ここでもう一段階チェックすることで、他人のデータへのアクセスを防ぐ。
     */
    public function view(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * これから作られるカテゴリに対しては、権限チェックが成立しない（まだ誰のものでもないため）。
     * 新しく作られたカテゴリは、必ず作った本人のものとして自動的に紐づくという仕組みが、
     * コントローラー側で保証されているため、他人になりすましてデータを作成できる余地はない。
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * 特定の1件のカテゴリが、ログイン中のユーザー自身のものかを判定する。
     * Global Scopeで既に絞り込んでいるが、
     * 万が一その実装にバグがあった場合でも、ここでもう一段階チェックすることで、他人のデータを更新される事態を防ぐ。
     * カテゴリ名が書き換えられると、category_idで紐づく過去の収支記録すべてに影響が及ぶため、慎重に扱う必要がある。
     */
    public function update(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * 特定の1件のカテゴリが、ログイン中のユーザー自身のものかを判定する。
     * Global Scopeで既に絞り込んでいるが、
     * 万が一その実装にバグがあった場合でも、ここでもう一段階チェックすることで、他人のデータを削除される事態を防ぐ。
     * なお、使用中のカテゴリを削除できないようにする制約は、ここではなくコントローラー側で行っている。
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }
}
