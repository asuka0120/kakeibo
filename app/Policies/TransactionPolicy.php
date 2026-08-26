<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
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
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
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
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * ユーザーがこのモデルを削除できるかどうかを判定する。
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }
}
