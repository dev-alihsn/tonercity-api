<?php

use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\artisan;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('stores uploaded image as media', function () {
    artisan('migrate');

    Storage::fake('public');

    $file = UploadedFile::fake()->image('photo.jpg');

    $service = app(MediaService::class);

    $media = $service->storeUploadedFile($file);

    expect($media)->toBeInstanceOf(Media::class)
        ->and($media->disk)->toBe('public')
        ->and($media->type)->toBe('image');

    Storage::disk('public')->assertExists($media->path);
});

