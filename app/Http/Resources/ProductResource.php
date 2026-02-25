<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = App::getLocale();
        $translation = $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en');

        return [
            'locale' => $locale,
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'name' => $translation?->name,
            'description' => $translation?->description,
            'is_active' => $this->is_active,
            'thumbnail_url' => $this->thumbnail?->getUrl(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'inventory' => $this->whenLoaded('inventory', fn () => [
                'quantity' => $this->inventory->quantity,
                'low_stock_level' => $this->inventory->low_stock_level,
                'in_stock' => $this->inventory->hasStock(1),
            ]),
        ];
    }
}
