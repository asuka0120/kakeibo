<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('収支記録') }}
            </h2>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('収支を登録') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                    <div>
                        <x-input-label for="date_from" :value="__('日付（開始）')" />
                        <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" value="{{ $filters['date_from'] ?? '' }}" />
                    </div>
                    <div>
                        <x-input-label for="date_to" :value="__('日付（終了）')" />
                        <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" value="{{ $filters['date_to'] ?? '' }}" />
                    </div>
                    <div>
                        <x-input-label for="category_id" :value="__('カテゴリ')" />
                        <x-select-input id="category_id" name="category_id" class="mt-1 block w-full">
                            <option value="">{{ __('すべて') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>
                                    {{ $category->name }}（{{ $category->type === 'income' ? __('収入') : __('支出') }}）
                                </option>
                            @endforeach
                        </x-select-input>
                    </div>
                    <div>
                        <x-input-label for="type" :value="__('種別')" />
                        <x-select-input id="type" name="type" class="mt-1 block w-full">
                            <option value="">{{ __('すべて') }}</option>
                            <option value="income" @selected(($filters['type'] ?? '') === 'income')>{{ __('収入') }}</option>
                            <option value="expense" @selected(($filters['type'] ?? '') === 'expense')>{{ __('支出') }}</option>
                        </x-select-input>
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button type="submit">{{ __('絞り込む') }}</x-primary-button>
                        <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 hover:text-gray-900 self-center">{{ __('クリア') }}</a>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($transactions->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('該当する収支記録がありません。') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('日付') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('種別') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('カテゴリ') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('金額') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('メモ') }}</th>
                                    <th class="px-3 py-2 w-32"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-800 whitespace-nowrap">{{ $transaction->date->format('Y-m-d') }}</td>
                                        <td class="px-3 py-2 text-sm whitespace-nowrap">
                                            <span class="{{ $transaction->type === 'income' ? 'text-blue-600' : 'text-red-600' }}">
                                                {{ $transaction->type === 'income' ? __('収入') : __('支出') }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-800">{{ $transaction->category->name }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-800 text-right whitespace-nowrap">
                                            {{ number_format($transaction->amount) }}{{ __('円') }}
                                        </td>
                                        <td class="px-3 py-2 text-sm text-gray-500">{{ $transaction->memo }}</td>
                                        <td class="px-3 py-2 text-right space-x-2 whitespace-nowrap">
                                            <a href="{{ route('transactions.edit', $transaction) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('編集') }}</a>
                                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('{{ __('この収支記録を削除しますか？') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-600 hover:text-red-900">{{ __('削除') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
