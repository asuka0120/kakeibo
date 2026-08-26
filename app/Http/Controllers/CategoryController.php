<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * リソースの一覧を表示する。
     */
    public function index(): View
    {
        $categories = auth()->user()
            ->categories()
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('categories.index', [
            'incomeCategories' => $categories->get(Category::TYPE_INCOME, collect()),
            'expenseCategories' => $categories->get(Category::TYPE_EXPENSE, collect()),
        ]);
    }

    /**
     * 新規作成用のフォームを表示する。
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * 新しく作成したリソースを保存する。
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $request->user()->categories()->create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('status', 'カテゴリを追加しました。');
    }

    /**
     * 指定されたリソースの編集フォームを表示する。
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * 指定されたリソースを更新する。
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('status', 'カテゴリを更新しました。');
    }

    /**
     * 指定されたリソースを削除する。
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        if ($category->transactions()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'このカテゴリは使用中のため削除できません。');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('status', 'カテゴリを削除しました。');
    }
}
