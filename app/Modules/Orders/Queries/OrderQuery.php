<?php

namespace App\Modules\Orders\Queries;

use App\Modules\Orders\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OrderQuery
{
    public function apply(array $filters): Builder
    {
        $query = Order::query()
            ->with([
                'client',
                'delivery',
                'orderProducts.product',
            ]);

        $this->applyDateFilter($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applySort($query, $filters);
        $this->applyClientFilter($query, $filters);
        $this->applyDeliveryFilter($query, $filters);
        $this->applyProductsFilter($query, $filters);

        return $query;
    }

    private function applyDateFilter(Builder $query, array $filters): void
    {
        $timezone = 'America/Argentina/Buenos_Aires';

        $start_date = isset($filters['date_from'])
            ? Carbon::parse($filters['date_from'], $timezone)->startOfDay()->utc()
            : Carbon::today($timezone)->startOfDay()->utc();

        $end_date = isset($filters['date_to'])
            ? Carbon::parse($filters['date_to'], $timezone)->endOfDay()->utc()
            : Carbon::today($timezone)->endOfDay()->utc();

        $query->whereBetween('created_at', [$start_date, $end_date])
            ->orWhereBetween('scheduled_at', [$start_date, $end_date]);
    }

    private function applyStatusFilter(Builder $query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }

    private function applySort(Builder $query, array $filters): void
    {
        if (isset($filters['sort'])) {
            $sortField = ltrim($filters['sort'], '-');
            $sortDirection = str_starts_with($filters['sort'], '-') ? 'desc' : 'asc';
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }


    public function applyClientFilter(Builder $query, array $filters): void
    {
        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
    }

    public function applyDeliveryFilter(Builder $query, array $filters): void
    {
        if (isset($filters['delivery_id'])) {
            $query->where('delivery_id', $filters['delivery_id']);
        }
    }

    public function applyProductsFilter(Builder $query, array $filters): void
    {
        if (isset($filters['product_ids'])) {
            $query->whereHas('orderProducts', function ($q) use ($filters) {
                $q->whereIn('product_id', $filters['product_ids']);
            });
        }
    }
}
