<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('カテゴリを編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf
                    @method('PUT')

                    @include('categories._form', ['category' => $category])

                    <div class="flex items-center justify-end mt-6 gap-3">
                        <a href="{{ route('categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('キャンセル') }}</a>
                        <x-primary-button>{{ __('更新する') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
