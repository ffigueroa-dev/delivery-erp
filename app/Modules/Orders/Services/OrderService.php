<?php

namespace App\Modules\Orders\Services;

use App\Modules\Clients\Models\Client;
use App\Modules\Orders\Actions\CancelOrder;
use App\Modules\Orders\Actions\DeliverOrder;
use App\Modules\Orders\Actions\ListOrders;
use App\Modules\Orders\Actions\StoreOrder;
use App\Modules\Orders\Actions\StoreOrderProduct;
use App\Modules\Orders\Actions\UpdateOrder;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Models\Order;
use App\Modules\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected ListOrders $listOrders,
        protected StoreOrderProduct $storeOrderProduct,
        protected StoreOrder $storeOrder,
        protected CancelOrder $cancelOrder,
        protected DeliverOrder $deliverOrder,
        protected UpdateOrder $updateOrder,
    ) {}

    public function listOrders(array $filters): LengthAwarePaginator
    {
        return $this->listOrders->execute($filters);
    }

    public function store(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $client = Client::findOrFail($data['client_id']);

            $products = Product::query()
                ->whereIn(
                    'id',
                    collect($data['products'])->pluck('id')
                )
                ->with('prices')
                ->get()
                ->keyBy('id');

            $items = [];
            $total = 0;

            foreach ($data['products'] as $item) {
                $product = $products[$item['id']];

                $price = $product->getPriceForClientType($client->type);

                $subtotal = $price->amount * $item['quantity'];

                $items[] = [
                    'product_id' => $product->id,
                    'current_price' => $price->amount,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];

                $total += $subtotal;
            }

            $order = $this->storeOrder->execute([
                'client_id' => $client->id,
                'delivery_id' => $data['delivery_id'],
                'notes' => $data['notes'] ?? null,
                'total_amount' => $total,
            ]);

            foreach ($items as $item) {
                $this->storeOrderProduct->execute([
                    ...$item,
                    'order_id' => $order->id,
                ]);
            }

            return $order->load('orderProducts');
        });
    }

    public function cancel(Order $order): Order
    {
        if ($order->status !== OrderStatus::PENDING) {
            throw ValidationException::withMessages([
                'order' => 'Only pending orders can be cancelled.',
            ]);
        }

        return $this->cancelOrder->execute($order);
    }
    public function deliver(Order $order): Order
    {
        if ($order->status !== OrderStatus::PENDING) {
            throw ValidationException::withMessages([
                'order' => 'Only pending orders can be cancelled.',
            ]);
        }

        return $this->deliverOrder->execute($order);
    }

    public function update(Order $order, array $data): Order
    {
        return $this->updateOrder->execute($order, $data);
    }
}
