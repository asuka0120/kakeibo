<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'date',
        'type',
        'amount',
        'memo',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeInMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    /**
     * Scope every query to the currently authenticated user so other
     * users' transactions are never visible, including via route
     * model binding.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('owner', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('transactions.user_id', auth()->id());
            }
        });
    }
}
