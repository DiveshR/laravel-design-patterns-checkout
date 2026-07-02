<?php

namespace App\Services\PatternCheckout\Inventory;

use App\Models\PatternProduct;
use App\Services\PatternCheckout\Inventory\Contracts\StockReserver;

class MySqlStockReserver implements StockReserver
{
    public function reserve(int $productId, int $quantity): bool
    {
        $affectedRows = PatternProduct::query()
            ->where('id', $productId)
            ->where('stock', '>=', $quantity)
            ->decrement('stock', $quantity);

        return $affectedRows === 1;
    }
}
