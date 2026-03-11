<?php

use App\Models\Category;

test('withTranslation scope loads the translation for the specified locale', function () {
    $category = Category::factory()->create();

    $enCategory = Category::withTranslation('en')->find($category->id);
    expect($enCategory->translations)->toHaveCount(1)
        ->and($enCategory->translations->first()->locale)->toBe('en');

    $arCategory = Category::withTranslation('ar')->find($category->id);
    expect($arCategory->translations)->toHaveCount(1)
        ->and($arCategory->translations->first()->locale)->toBe('ar');
});

test('withTranslation scope defaults to app locale', function () {
    app()->setLocale('ar');

    $category = Category::factory()->create();

    $fetched = Category::withTranslation()->find($category->id);
    expect($fetched->translations)->toHaveCount(1)
        ->and($fetched->translations->first()->locale)->toBe('ar');
});
