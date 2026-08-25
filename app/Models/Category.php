<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public const TYPE_INCOME = 'income';

    public const TYPE_EXPENSE = 'expense';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * カテゴリの取得は必ずログイン中のユーザーのものだけに絞り込む。
     *
     * URLのIDを他人のものに書き換えられても、Policyの「権限がない」
     * エラーではなく「そもそも存在しない」扱いにすることで、
     * 他人のデータが存在すること自体を悪意ある第三者に知られないようにする。
     */
    protected static function booted(): void
    {
        static::addGlobalScope('owner', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('categories.user_id', auth()->id());
            }
        });
    }
}
