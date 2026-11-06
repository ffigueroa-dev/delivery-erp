<?php

namespace App\Modules\Product\Actions;

use App\Modules\Product\Queries\ProductQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListProducts
{
    public function __construct(
        private ProductQuery $productQuery,
    ) {}
    public function execute(array $filters): LengthAwarePaginator
    {
        return $this->productQuery
            ->apply($filters)
            ->paginate($filters['per_page'])
            ->withQueryString();
    }
}
