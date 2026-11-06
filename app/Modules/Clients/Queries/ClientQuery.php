<?php

namespace App\Modules\Clients\Queries;

use App\Modules\Clients\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ClientQuery
{
    public function apply(array $filters): Builder
    {
        $query = Client::query();

        $this->applyActiveFilter($query, $filters);
        $this->applyTypeFilter($query, $filters);
        $this->applySearchFilter($query, $filters);
        $this->applyPhoneFilter($query, $filters); 
        $this->applyAddressFilter($query, $filters);


        return $query;
    }

    private function applyActiveFilter(Builder $query, array $filters): void
    {
        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }
    }

    private function applyTypeFilter(Builder $query, array $filters): void
    {
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
    }

    private function applySearchFilter(Builder $query, array $filters): void
    {
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
    }

    private function applyPhoneFilter(Builder $query, array $filters): void
    {
        if (isset($filters['phone'])) {
            $query->where('phone', 'like', '%' . $filters['phone'] . '%');
        }
    }

    private function applyAddressFilter(Builder $query, array $filters): void
    {
        if (isset($filters['address'])) {
            $query->where('full_address', 'like', '%' . $filters['address'] . '%');
        }
    }

}
