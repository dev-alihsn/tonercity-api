<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(ListProductsRequest $request): AnonymousResourceCollection
    {
        $query = Product::query()
            ->where('is_active', true)
            ->whereHas('translations', fn ($q) => $q->where('locale', app()->getLocale()))
            ->with(['translations', 'thumbnail', 'inventory', 'categories.translations']);

        if ($request->filled('category_id')) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $request->validated('category_id')));
        }

        if ($request->filled('search')) {
            $search = $request->validated('search');
            $query->whereHas('translations', fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"));
        }

        // $locale = $request->input('locale', 'en');
        $locale = app()->getLocale();

        match ($request->input('sort', 'newest')) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->join('product_translations', fn ($j) => $j->on('product_translations.product_id', '=', 'products.id')
                ->where('product_translations.locale', $locale))
                ->orderBy('product_translations.title', 'asc')
                ->select('products.*'),
            'name_desc' => $query->join('product_translations', fn ($j) => $j->on('product_translations.product_id', '=', 'products.id')
                ->where('product_translations.locale', $locale))
                ->orderBy('product_translations.title', 'desc')
                ->select('products.*'),
            default => $query->orderByDesc('created_at'),
        };

        $perPage = $request->input('per_page', 15);

        return ProductResource::collection($query->paginate($perPage));
    }

    public function show(Product $product): ProductResource
    {
        $product->load(['translations', 'thumbnail', 'inventory', 'categories.translations']);

        if (! $product->is_active) {
            abort(404);
        }

        return new ProductResource($product);
    }
}
