<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function storeUploadedFile(
        UploadedFile $file,
        ?string $disk = null,
        ?string $type = null,
        ?string $alt = null,
    ): Media {
        $disk ??= 'public';

        $path = $file->store('media', $disk);

        $mime = $file->getMimeType() ?? '';
        $resolvedType = $type ?? $this->guessTypeFromMime($mime);

        return Media::query()->create([
            'disk' => $disk,
            'path' => $path,
            'type' => $resolvedType,
            'alt' => $alt,
        ]);
    }

    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);

        $media->delete();
    }

    public function attachToProduct(Product $product, Media $media, ?int $sortOrder = null): void
    {
        $sortOrder ??= $product->media()->max('pivot_sort_order') + 1;

        $product->media()->attach($media->id, [
            'sort_order' => $sortOrder,
        ]);
    }

    private function guessTypeFromMime(string $mime): string
    {
        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }

        return 'file';
    }
}

