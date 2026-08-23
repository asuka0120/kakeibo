<?php

namespace App\Listeners;

use App\Models\Category;
use Illuminate\Auth\Events\Registered;

class CreateDefaultCategories
{
    /**
     * Default categories created for every newly registered user.
     *
     * @var array<string, list<string>>
     */
    private const DEFAULTS = [
        Category::TYPE_INCOME => ['給与', '賞与', '副業', 'その他収入'],
        Category::TYPE_EXPENSE => ['食費', '交通費', '住居費', '光熱費', '通信費', '娯楽費', '医療費', 'その他支出'],
    ];

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $rows = [];

        foreach (self::DEFAULTS as $type => $names) {
            foreach ($names as $name) {
                $rows[] = [
                    'user_id' => $event->user->id,
                    'name' => $name,
                    'type' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Category::insert($rows);
    }
}
