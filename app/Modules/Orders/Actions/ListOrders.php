<?php

namespace App\Modules\Orders\Actions;

use App\Modules\Orders\Queries\OrderQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListOrders
{
    public function __construct(
        private OrderQuery $orderQuery,
    ) {}

    public function execute(array $filters): LengthAwarePaginator
    {
        return $this->orderQuery
            ->apply($filters)
            ->paginate($filters['per_page'])
            ->withQueryString();
    }
}
