<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * ユーザーが一覧を閲覧できるかどうかを判定する。
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * ユーザーがこのモデルを閲覧できるかどうかを判定する。
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
