<?php

namespace App\Scopes;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Builder;

trait OrderScope
{
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereIn('status', [
            OrderStatusEnum::PROCESSING->value,
            OrderStatusEnum::SHIPPED->value,
            OrderStatusEnum::DELIVERED->value
        ]);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }
}