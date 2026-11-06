<?php

namespace App\Modules\Product\Queries;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductQuery
{
    public function apply(array $filters): Builder
    {
        $query = Product::query()
            ->with('prices');

        $this->applySearchFilter($query, $filters);

        return $query;
    }

    private function applySearchFilter(Builder $query, array $filters): void
    {
        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }
    }
}
