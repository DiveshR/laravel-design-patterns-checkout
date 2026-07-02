<?php

namespace App\Services\PatternCheckout\Decorators;

use App\Services\PatternCheckout\Contracts\CheckoutService;
use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\DTO\CheckoutResult;
use App\Services\PatternCheckout\Inventory\Contracts\StockReserver;

class StockProtectedCheckoutDecorator implements CheckoutService
{
    public function __construct(
        private CheckoutService $next,
        private StockReserver $stockReserver,
    ) {}

    public function checkout(CheckoutCommand $command): CheckoutResult
    {
        $reserved = $this->stockReserver->reserve($command->productId, $command->quantity);

        if (! $reserved) {
            return CheckoutResult::failed('Product is out of stock.');
        }

        return $this->next->checkout($command);
    }
}
