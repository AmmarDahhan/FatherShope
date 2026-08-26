<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->when($request->filled('category'), fn ($q, $cat) => $q->where('category', $cat))
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ProductResource::collection($products);
    }

    public function show(string $id): ProductResource
    {
        $product = Product::findOrFail($id);

        return new ProductResource($product);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        $query = $request->input('q', '');

        $products = Product::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            })
            ->active()
            ->latest()
            ->paginate(15);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProductRequest $request, string $id): ProductResource
    {
        $product = Product::findOrFail($id);
        $product->update($request->validated());

        return new ProductResource($product);
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => false]);

        return response()->json(['message' => 'Product deactivated successfully.']);
    }
}
