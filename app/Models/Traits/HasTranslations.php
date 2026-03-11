<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTranslations
{
    /**
     * Scope a query to include translations for the given locale.
     */
    public function scopeWithTranslation(Builder $query, ?string $locale = null): void
    {
        $locale = $locale ?? app()->getLocale();

        $query->with(['translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }]);
    }
}
