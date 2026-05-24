<?php

namespace App\Http\Controllers;

use App\DTOs\ProductQuickDTO;
use App\DTOs\StoreProductDTO;
use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\Product\ProductQuickRequest;
use App\Http\Requests\Product\ProductSearchRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'category_id', 'low_stock', 'per_page', 'is_active']);
            $products = $this->productService->list($request->user(), $filters);

            return $this->success(
                ProductResource::collection($products),
                'Products retrieved successfully',
                200,
                ['meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]]
            );
        } catch (\Throwable $e) {
            Log::error('Product list failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to retrieve products');
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $dto = StoreProductDTO::fromRequest($request->validated());
            $product = $this->productService->createProduct($request->user(), $dto);

            return $this->created(
                new ProductResource($product),
                'Product created successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Product create failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to create product');
        }
    }

    public function update(UpdateProductRequest $request, string $uuid): JsonResponse
    {
        try {
            $dto = StoreProductDTO::fromRequest($request->validated());
            $product = $this->productService->updateProduct($request->user(), $uuid, $dto);

            return $this->success(
                new ProductResource($product),
                'Product updated successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Product update failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to update product');
        }
    }

    public function destroy(string $uuid): JsonResponse
    {
        try {
            $this->productService->deleteProduct(request()->user(), $uuid);

            return $this->noContent('Product deleted successfully');
        } catch (\Throwable $e) {
            Log::error('Product delete failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to delete product');
        }
    }

    public function search(ProductSearchRequest $request): JsonResponse
    {
        try {
            $products = $this->productService->search(
                $request->user(),
                $request->validated('q') ?? '',
                (int) ($request->validated('limit') ?? 20)
            );

            return $this->success(
                ProductResource::collection($products),
                'Products retrieved successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Product search failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to search products');
        }
    }

    public function quickAdd(ProductQuickRequest $request): JsonResponse
    {
        try {
            $dto = ProductQuickDTO::fromRequest($request->validated());
            $product = $this->productService->quickAdd($request->user(), $dto);

            return $this->created(
                new ProductResource($product),
                'Product created successfully'
            );
        } catch (\Throwable $e) {
            Log::error('Product quick add failed', ['error' => $e->getMessage()]);

            return $this->error($e->getMessage() ?: 'Failed to create product');
        }
    }

    public function show(string $uuid): JsonResponse
    {
        try {
            $product = $this->productService->getProduct(
                request()->user(),
                $uuid
            );

            if (! $product) {
                throw new ProductNotFoundException;
            }

            return $this->success(
                new ProductResource($product),
                'Product retrieved successfully'
            );
        } catch (ProductNotFoundException $e) {
            return $this->notFound($e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Product fetch failed', ['uuid' => $uuid, 'error' => $e->getMessage()]);

            return $this->error('Failed to retrieve product');
        }
    }
}
