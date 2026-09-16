<?php

namespace App\Support;

class VehicleStockStatus
{
    public static function for(int $stockQuantity): string
    {
        return $stockQuantity > 0 ? 'disponible' : 'epuise';
    }
}
