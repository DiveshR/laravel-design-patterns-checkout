<?php

namespace App\Services\PatternCheckout\Inventory\Contracts;

interface StockReserver
{
    public function reserve(int $productId, int $quantity): bool;
}
