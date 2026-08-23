@if ($categories->isEmpty())
    <p class="text-sm text-gray-500">{{ __('カテゴリがありません。') }}</p>
@else
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('カテゴリ名') }}</th>
                <th class="px-3 py-2 w-40"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($categories as $category)
                <tr>
                    <td class="px-3 py-2 text-sm text-gray-800">{{ $category->name }}</td>
                    <td class="px-3 py-2 text-right space-x-2 whitespace-nowrap">
                        <a href="{{ route('categories.edit', $category) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('編集') }}</a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline"
                              onsubmit="return confirm('{{ __('このカテゴリを削除しますか？') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-900">{{ __('削除') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
