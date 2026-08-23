<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('月別集計') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                <a href="{{ route('dashboard', ['year' => $prev->year, 'month' => $prev->month]) }}"
                   class="text-sm text-gray-600 hover:text-gray-900">
                    &laquo; {{ __('前月') }}
                </a>

                <div class="text-lg font-semibold text-gray-800">
                    {{ $year }}{{ __('年') }}{{ $month }}{{ __('月') }}
                </div>

                <a href="{{ route('dashboard', ['year' => $next->year, 'month' => $next->month]) }}"
                   class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('翌月') }} &raquo;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">{{ __('収入合計') }}</div>
                    <div class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($income) }}{{ __('円') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">{{ __('支出合計') }}</div>
                    <div class="mt-1 text-2xl font-bold text-red-600">{{ number_format($expense) }}{{ __('円') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">{{ __('収支') }}</div>
                    <div class="mt-1 text-2xl font-bold {{ $balance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        {{ $balance >= 0 ? '+' : '' }}{{ number_format($balance) }}{{ __('円') }}
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('カテゴリ別内訳') }}</h3>

                @if ($categoryTotals->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('この月の収支記録はまだありません。') }}</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('種別') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('カテゴリ') }}</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('金額') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($categoryTotals as $row)
                                <tr>
                                    <td class="px-3 py-2 text-sm whitespace-nowrap">
                                        <span class="{{ $row->type === 'income' ? 'text-blue-600' : 'text-red-600' }}">
                                            {{ $row->type === 'income' ? __('収入') : __('支出') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-800">{{ $row->category->name ?? __('（削除済み）') }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-800 text-right whitespace-nowrap">
                                        {{ number_format($row->total) }}{{ __('円') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
