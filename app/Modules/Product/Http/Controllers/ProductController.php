<?php

namespace App\Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Enums\PriceType;
use App\Modules\Product\Http\Requests\IndexProductRequest;
use App\Modules\Product\Http\Requests\StoreProductRequest;
use App\Modules\Product\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Http\Resources\ProductResource;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\ProductService;
use App\Support\Toast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{

    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService =  $productService;
    }

    public function index(IndexProductRequest $request): Response
    {
        $filters = $request->validated();
        $products =  $this->productService->listProducts($filters);
        return Inertia::render('product/Index', [
            'products' => ProductResource::collection($products),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('product/Create', [
            'priceTypes' => collect(PriceType::cases())
                ->map(fn(PriceType $type) => [
                    'value' => $type->value,
                    'label' => $type->label(),
                ]),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $this->productService->storeProduct($data);

            return redirect()
                ->route('products')
                ->with('success', 'Product created successfully.');
        } catch (\Throwable $th) {

            Log::error('Error creating product', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->validated(),
            ]);

            return redirect()
                ->back()
                ->withErrors([
                    'pageError' => 'There was an error creating the product. Please try again or contact support.',
                ]);
        }
    }

    public function delete(Product $product): RedirectResponse
    {
        try {
            $this->productService->deleteProduct($product);

            Toast::success('Product deleted successfully.');

            return redirect()->back();
        } catch (\Throwable $th) {
            Log::error('Error deleting product', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'data' => $product,
            ]);

            Toast::error('There was an error deleting the product.');

            return redirect()->back();
        }
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('product/Edit', [
            'product' => new ProductResource($product->load('prices')),
            'priceTypes' => collect(PriceType::cases())
                ->map(fn(PriceType $type) => [
                    'value' => $type->value,
                    'label' => $type->label(),
                ]),
        ]);
    }

    public function update(Product $product, UpdateProductRequest $request): RedirectResponse
    {
        try {
            $data =  $request->validated();
            $this->productService->updateProduct($product, $data);
            Toast::success('Product updated successfully.');
            return redirect()
                ->route('products');
        } catch (\Throwable $th) {
            Log::error('Error updating prodcut', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->validated()
            ]);

            Toast::error('There was an error updating the product. Please try again later');
            return redirect()
                ->back();
        }
    }
}
