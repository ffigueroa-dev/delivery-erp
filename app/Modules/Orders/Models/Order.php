<?php

namespace App\Modules\Orders\Models;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Order\Database\Factories\OrderFactory;
use App\Modules\Orders\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasUuids, HasFactory;
    protected $fillable = [
        'client_id',
        'delivery_id',
        'status',
        'notes',
        'total_amount'
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class
        ];
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_id');
    }

    protected static function  newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
