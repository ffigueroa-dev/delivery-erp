<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Actions\DeleteProduct;
use App\Modules\Product\Actions\ListProducts;
use App\Modules\Product\Actions\StoreProduct;
use App\Modules\Product\Actions\UpdateProduct;
use App\Modules\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    protected $storeProduct;
    protected $updateProduct;
    protected $deleteProduct;
    protected $listProducts;

    public function __construct(StoreProduct $storeProduct, UpdateProduct $updateProduct, DeleteProduct $deleteProduct, ListProducts $listProducts)
    {
        $this->storeProduct =  $storeProduct;
        $this->updateProduct =  $updateProduct;
        $this->deleteProduct =  $deleteProduct;
        $this->listProducts =  $listProducts;
    }

    public function storeProduct($data): Product
    {
        return $this->storeProduct->execute($data);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return $this->updateProduct->execute($product, $data);
    }

    public function deleteProduct(Product $product): Product
    {
        return $this->deleteProduct->execute($product);
    }

    public function listProducts( array $filters): LengthAwarePaginator
    {
        return $this->listProducts->execute($filters);
    }
}
