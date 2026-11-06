<?php

namespace App\Modules\Clients\Actions;

use App\Modules\Clients\Queries\ClientQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListClients
{
    public function __construct(
        private ClientQuery $clientQuery,
    ) {}

    public function execute(array $filters): LengthAwarePaginator
    {
        return $this->clientQuery
            ->apply($filters)
            ->paginate($filters['per_page'])
            ->withQueryString();
    }

}
