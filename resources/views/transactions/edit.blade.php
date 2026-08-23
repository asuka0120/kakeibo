<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('収支を編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('transactions.update', $transaction) }}">
                    @csrf
                    @method('PUT')

                    @include('transactions._form', ['transaction' => $transaction, 'categories' => $categories])

                    <div class="flex items-center justify-end mt-6 gap-3">
                        <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('キャンセル') }}</a>
                        <x-primary-button>{{ __('更新する') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
