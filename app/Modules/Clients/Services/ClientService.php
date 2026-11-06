<?php

namespace App\Modules\Clients\Services;

use App\Modules\Clients\Actions\DeleteClient;
use App\Modules\Clients\Actions\ListClients;
use App\Modules\Clients\Actions\StoreClient;
use App\Modules\Clients\Actions\UpdateClient;
use App\Modules\Clients\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ClientService
{
    public function __construct(
        protected ListClients $listClients,
        protected StoreClient $storeClient,
        protected UpdateClient $updateClient,
        protected DeleteClient $deleteClient,
    ) {}

    public function listClients(array $filters ): LengthAwarePaginator
    {
        return $this->listClients->execute($filters);
    }
    public function storeClient(array $data): Client
    {
        return $this->storeClient->execute($data);
    }
    public function updateClient(Client $client, array $data)
    {
        return $this->updateClient->execute($client, $data);
    }
    public function deleteClient(Client $client): Client
    {
        return $this->deleteClient->execute($client);
    }
}
