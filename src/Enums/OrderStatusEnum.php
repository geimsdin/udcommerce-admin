<?php

namespace Unusualdope\LaravelEcommerce\Enums;

enum OrderStatusEnum: int
{
    case WAITING = 1;
    case PAID = 2;
    case SHIPPED = 3;
    case DELIVERED = 4;
    case CANCELLED = 5;

    public function label(): string
    {
        return match ($this) {
            self::WAITING => 'In attesa',
            self::PAID => 'Pagato',
            self::SHIPPED => 'Spedito',
            self::DELIVERED => 'Consegnato',
            self::CANCELLED => 'Annullato',
        };
    }
}
