@props(['category' => null])

<div>
    <x-input-label for="name" :value="__('カテゴリ名')" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
        value="{{ old('name', $category->name ?? '') }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="type" :value="__('種別')" />
    <x-select-input id="type" name="type" class="mt-1 block w-full" required>
        <option value="expense" @selected(old('type', $category->type ?? 'expense') === 'expense')>{{ __('支出') }}</option>
        <option value="income" @selected(old('type', $category->type ?? '') === 'income')>{{ __('収入') }}</option>
    </x-select-input>
    <x-input-error :messages="$errors->get('type')" class="mt-2" />
</div>
