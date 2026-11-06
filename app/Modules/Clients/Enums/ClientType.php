<?php
namespace App\Modules\Clients\Enums;


enum ClientType: string
{
    case RETAIL = 'retail';
    case COMMERCIAL = 'commercial';

    public function label(): string
    {
        return match ($this) {
            self::RETAIL => 'Retail',
            self::COMMERCIAL => 'Commercial',
        };
    }
    public static function values(): array
    {
        return array_map(fn($type) => $type->value, self::cases());
    }
}
