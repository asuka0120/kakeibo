<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * リソースの一覧を表示する。
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $transactions = $user->transactions()
            ->with('category')
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('date', '<=', $request->date('date_to')))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $categories = $user->categories()->orderBy('type')->orderBy('name')->get();

        return view('transactions.index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'filters' => $request->only(['date_from', 'date_to', 'category_id', 'type']),
        ]);
    }

    /**
     * 新規作成用のフォームを表示する。
     */
    public function create(Request $request): View
    {
        return view('transactions.create', [
            'categories' => $request->user()->categories()->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    /**
     * 新しく作成したリソースを保存する。
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $request->user()->transactions()->create($request->validated());

        return redirect()
            ->route('transactions.index')
            ->with('status', '収支を登録しました。');
    }

    /**
     * 指定されたリソースの編集フォームを表示する。
     */
    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);

        return view('transactions.edit', [
            'transaction' => $transaction,
            'categories' => auth()->user()->categories()->orderBy('type')->orderBy('name')->get(),
        ]);
    }

    /**
     * 指定されたリソースを更新する。
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $transaction->update($request->validated());

        return redirect()
            ->route('transactions.index')
            ->with('status', '収支を更新しました。');
    }

    /**
     * 指定されたリソースを削除する。
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('status', '収支を削除しました。');
    }
}
