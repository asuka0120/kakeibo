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
     * ユーザーが作成できるかどうかを判定する。
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ユーザーがこのモデルを更新できるかどうかを判定する。
     */
    public function update(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    /**
     * ユーザーがこのモデルを削除できるかどうかを判定する。
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }
}
