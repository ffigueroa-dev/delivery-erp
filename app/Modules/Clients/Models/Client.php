<?php

namespace App\Modules\Clients\Models;

use App\Modules\Clients\Database\Factories\ClientFactory;
use App\Modules\Clients\Enums\ClientType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Client extends Model
{
    use HasUuids, HasFactory;
    protected $attributes = ['active' => true];
    protected $fillable = [
        'name',
        'phone',
        'type',
        'full_address',
        'address_reference',
        'active'
    ];
    #[Override]
    protected function casts()
    {
        return [
            'type' => ClientType::class,
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    protected static function newFactory(): ClientFactory
    {
        return ClientFactory::new();
    }
}
