<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
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
     * 特定の1件の収支記録が、ログイン中のユーザー自身のものかを判定する。
     * Global Scopeで既に絞り込んでいるが、
     * 万が一その実装にバグがあった場合でも、ここでもう一段階チェックすることで、他人のデータへのアクセスを防ぐ。
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * これから作られる収支記録に対しては、権限チェックが成立しない（まだ誰のものでもないため）。
     * 新しく作られた収支記録は、必ず作った本人のものとして自動的に紐づくという仕組みが、
     * コントローラー側で保証されているため、他人になりすましてデータを作成できる余地はない。
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * 特定の1件の収支記録が、ログイン中のユーザー自身のものかを判定する。
     * Global Scopeで既に絞り込んでいるが、
     * 万が一その実装にバグがあった場合でも、ここでもう一段階チェックすることで、他人のデータを更新される事態を防ぐ。
     * 収支記録の金額が勝手に書き換えられると、月別集計の収入合計・支出合計・収支の数字が、実際の家計と合わなくなってしまうため。
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * 特定の1件の収支記録が、ログイン中のユーザー自身のものかを判定する。
     * Global Scopeで既に絞り込んでいるが、
     * 万が一その実装にバグがあった場合でも、ここでもう一段階チェックすることで、他人のデータを削除される事態を防ぐ。
     * なお、カテゴリと違い収支記録には使用中による削除制約はなく、自分のものであれば自由に削除できる。
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }
}
