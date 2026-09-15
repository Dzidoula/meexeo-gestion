<?php

namespace App\Support;

class ProductStockStatus
{
    /** Jamais stocké : recalculé à chaque affichage depuis stock_quantity. */
    public static function for(int $stockQuantity): string
    {
        return $stockQuantity > 0 ? 'en_stock' : 'rupture';
    }
}
