<?php

namespace App\Modules\Orders\Http\Controllers;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Orders\Http\Requests\IndexOrderRequest;
use App\Modules\Orders\Http\Requests\StoreOrderRequest;
use App\Modules\Orders\Http\Requests\UpdateOrderRequest;
use App\Modules\Orders\Http\Resources\OrderResource;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Services\OrderService;
use App\Modules\Product\Http\Resources\ProductResource;
use App\Modules\Product\Models\Product;
use App\Support\Toast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;


class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(IndexOrderRequest $request): Response
    {
        $orders = $this->orderService->listOrders($request->validated());

        $clients = Client::query()
            ->active()
            ->get()
            ->map(fn($client) => [
                'value' => $client->id,
                'label' => $client->name
            ]);

        $deliveries = User::query()
            ->role(Role::DELIVERY->value)
            ->get()
            ->map(fn($delivery) => [
                'value' => $delivery->id,
                'label' => $delivery->name
            ]);

        $products = Product::query()
            ->active()
            ->get()
            ->map(fn($product) => [
                'value' => $product->id,
                'label' => $product->name
            ]);

        return Inertia::render('order/Index', [
            'orders' => OrderResource::collection($orders),
            'filters' => $request->validated(),
            'dropdowns' => [
                'clients' => $clients,
                'deliveries' => $deliveries,
                'products' => $products,
            ],
        ]);
    }

    public function create(): Response
    {
        $products = Product::query()
            ->active()
            ->with('prices')
            ->get();

        $clients = Client::query()
            ->active()
            ->get()
            ->map(fn($client) => [
                'value' => $client->id,
                'label' => $client->name
            ]);

        $deliveries = User::query()
            ->role(Role::DELIVERY->value)
            ->get()
            ->map(fn($delivery) => [
                'value' => $delivery->id,
                'label' => $delivery->name
            ]);

        return Inertia::render('order/Create', [
            'products' => ProductResource::collection($products),
            'dropdowns' => [
                'clients' => $clients,
                'deliveries' => $deliveries,
            ],
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        try {
            $data =  $request->validated();
            $this->orderService->store($data);
            Toast::success('Order created successfully');

            return redirect()->route('orders.index');
        } catch (\Throwable $th) {
            Log::error('Failed to store order', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->validated()
            ]);
            Toast::error('There was an error creating order. Please try again later');
            return redirect()->back();
        }
    }

    public function cancel(Order $order): RedirectResponse
    {
        try {
            $this->orderService->cancel($order);

            Toast::success('Order cancelled successfully');

            return redirect()->back();
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $th) {
            Log::error('Failed to cancel order', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'order_id' => $order->id,
            ]);

            Toast::error(
                'There was an error cancelling the order. Please try again later'
            );

            return redirect()->back();
        }
    }
    public function deliver(Order $order): RedirectResponse
    {
        try {
            $this->orderService->cancel($order);

            Toast::success('Order delivered successfully');

            return redirect()
                ->route('orders.index');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $th) {
            Log::error('Failed to deliver order', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'order_id' => $order->id,
            ]);

            Toast::error(
                'There was an error delivering the order. Please try again later'
            );

            return redirect()->back();
        }
    }

    public function edit(Order $order): Response
    {
        $productIds = $order->orderProducts()
            ->pluck('product_id');

        $products = Product::query()
            ->where(function ($query) use ($productIds) {
                $query
                    ->active()
                    ->orWhereIn('id', $productIds);
            })
            ->with('prices')
            ->get();

        $clients = Client::query()
            ->where(function ($query) use ($order) {
                $query
                    ->active()
                    ->orWhere('id', $order->client_id);
            })
            ->get()
            ->map(fn($client) => [
                'value' => $client->id,
                'label' => $client->name,
            ]);

        $deliveries = User::query()
            ->role(Role::DELIVERY->value)
            ->where(function ($query) use ($order) {
                $query
                    // ->active() //TODO: implement active or inactive status at users
                    ->orWhere('id', $order->delivery_id);
            })
            ->get()
            ->map(fn($delivery) => [
                'value' => $delivery->id,
                'label' => $delivery->name,
            ]);

        $order->load(['client', 'delivery', 'orderProducts.product']);

        return Inertia::render('order/Edit', [
            'products' => ProductResource::collection($products),
            'dropdowns' => [
                'clients' => $clients,
                'deliveries' => $deliveries,
            ],
            'order' => new OrderResource($order)
        ]);
    }

    public function update(Order $order, UpdateOrderRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $this->orderService->update($order, $data);
            Toast::success('Order updated successfully');
            return redirect()
                ->route('orders.detail', ['order' => $order]);
        } catch (\Throwable $th) {
            Log::error('Error updating prodcut', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->validated()
            ]);

            Toast::error('There was an error updating the order. Please try again later');
            return redirect()
                ->back();
        }
    }

    public function detail(Order $order): Response
    {
        $orderData = $order->load([
            'client',
            'delivery',
            'orderProducts.product',
        ]);;
        return Inertia::render('order/Detail', ['order' => new OrderResource($orderData)]);
    }
}
