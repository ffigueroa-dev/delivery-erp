<?php

namespace App\Modules\Product\Models;

use App\Modules\Clients\Enums\ClientType;
use App\Modules\Product\Database\Factories\ProductFactory;
use App\Modules\Product\Enums\PriceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Product\Models\ProductPrice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'active'
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function getPriceForClientType(ClientType $clientType): ProductPrice
    {
        $price = $this->prices
            ->firstWhere('type', $clientType->value);

        if ($price) {
            return $price;
        }

        return $this->prices
            ->firstWhere('type', PriceType::RETAIL->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }


    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
