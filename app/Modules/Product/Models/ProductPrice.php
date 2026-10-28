<?php

namespace App\Modules\Product\Models;

use App\Modules\Product\Database\Factories\ProductPriceFactory;
use App\Modules\Product\Enums\PriceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $product_id
 * @property PriceType $type
 * @property int $amount
 */
#[Fillable([
    'product_id',
    'type',
    'amount',
])]
class ProductPrice extends Model
{
    use HasUuids, HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;


    protected function casts(): array
    {
        return [
            'type' => PriceType::class,
            'amount' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function newFactory(): ProductPriceFactory
    {
        return ProductPriceFactory::new();
    }
}
