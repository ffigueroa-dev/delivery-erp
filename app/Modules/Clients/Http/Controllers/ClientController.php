<?php

namespace App\Modules\Clients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Clients\Enums\ClientType;
use App\Modules\Clients\Http\Requests\IndexClientRequest;
use App\Modules\Clients\Http\Requests\StoreClientRequest;
use App\Modules\Clients\Http\Requests\UpdateClientRequest;
use App\Modules\Clients\Http\Resources\ClientResource;
use App\Modules\Clients\Models\Client;
use App\Modules\Clients\Services\ClientService;
use App\Support\Toast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService,
    ) {}

    public function index(IndexClientRequest $request): Response
    {
        $filters = $request->validated();
        $clients =  $this->clientService->listClients($filters);
        return Inertia::render(
            'client/Index',
            [
                'clients' => ClientResource::collection($clients),
                'filters' => $filters
            ]
        );
    }

    public function create(): Response
    {
        return Inertia::render('client/Create', [
            'clientTypes' => collect(ClientType::cases())
                ->map(fn(ClientType $type) => [
                    'value' => $type->value,
                    'label' => $type->label(),
                ]),
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $this->clientService->storeClient($data);
            return redirect()
                ->route('clients.index');
        } catch (\Throwable $th) {
            Log::error('Error creating client', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->validated()
            ]);
            Toast::error('There was an error creating the client');
            return redirect()->back();
        }
    }

    public function edit(Client $client): Response
    {
        return Inertia::render('client/Edit', [
            'client' => new ClientResource($client),
            'clientTypes' => collect(ClientType::cases())
                ->map(fn(ClientType $type) => [
                    'value' => $type->value,
                    'label' => $type->label(),
                ]),
        ]);
    }

    public function update(Client $client, UpdateClientRequest $request): RedirectResponse
    {
        try {
            $data =  $request->validated();
            $this->clientService->updateClient($client, $data);
            Toast::success('Client updated successfully');

            return redirect()
                ->route('clients.index');
        } catch (\Throwable $th) {
            Log::error('Error updating client', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->validated()
            ]);
            Toast::error('There was an error updating the client');
            return redirect()->back();
        }
    }

    public function delete(Client $client): RedirectResponse
    {
        try {
            $this->clientService->deleteClient($client);
            Toast::success('Client deleted successfully');

            return redirect()
                ->route('clients.index');
        } catch (\Throwable $th) {
            Log::error('Error deleting client', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'data' => $client
            ]);
            Toast::error('There was an error deleting the client');
            return redirect()->back();
        }
    }

}
