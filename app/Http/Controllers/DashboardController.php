<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the monthly income / expense summary.
     */
    public function __invoke(Request $request): View
    {
        $year = (int) $request->integer('year', now()->year);
        $month = (int) $request->integer('month', now()->month);

        // Normalize an out-of-range month/year (e.g. from prev/next navigation).
        $current = Carbon::create($year, $month, 1);
        $year = $current->year;
        $month = $current->month;

        $user = $request->user();

        $totals = $user->transactions()
            ->inMonth($year, $month)
            ->selectRaw('type, COALESCE(SUM(amount), 0) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $income = (float) ($totals[Category::TYPE_INCOME] ?? 0);
        $expense = (float) ($totals[Category::TYPE_EXPENSE] ?? 0);

        $categoryTotals = $user->transactions()
            ->inMonth($year, $month)
            ->with('category')
            ->selectRaw('category_id, type, COALESCE(SUM(amount), 0) as total')
            ->groupBy('category_id', 'type')
            ->orderByDesc('total')
            ->get();

        return view('dashboard', [
            'year' => $year,
            'month' => $month,
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'categoryTotals' => $categoryTotals,
            'prev' => $current->copy()->subMonth(),
            'next' => $current->copy()->addMonth(),
        ]);
    }
}
