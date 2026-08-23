@props(['transaction' => null, 'categories'])

<div x-data="{ type: '{{ old('type', $transaction->type ?? 'expense') }}' }">
    <div>
        <x-input-label for="date" :value="__('日付')" />
        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full"
            value="{{ old('date', optional($transaction?->date)->format('Y-m-d')) }}" required />
        <x-input-error :messages="$errors->get('date')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="type" :value="__('種別')" />
        <x-select-input id="type" name="type" class="mt-1 block w-full" x-model="type" required>
            <option value="expense">{{ __('支出') }}</option>
            <option value="income">{{ __('収入') }}</option>
        </x-select-input>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="category_id" :value="__('カテゴリ')" />
        <x-select-input id="category_id" name="category_id" class="mt-1 block w-full" required>
            <option value="">{{ __('選択してください') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    x-show="type === '{{ $category->type }}'"
                    @selected(old('category_id', $transaction->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </x-select-input>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="amount" :value="__('金額')" />
        <x-text-input id="amount" name="amount" type="number" step="1" min="1" class="mt-1 block w-full"
            value="{{ old('amount', $transaction->amount ?? '') }}" required />
        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="memo" :value="__('メモ')" />
        <x-text-input id="memo" name="memo" type="text" class="mt-1 block w-full"
            value="{{ old('memo', $transaction->memo ?? '') }}" />
        <x-input-error :messages="$errors->get('memo')" class="mt-2" />
    </div>
</div>
