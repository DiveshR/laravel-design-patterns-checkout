<?php

namespace App\Services\PatternCheckout\Decorators;

use App\Services\PatternCheckout\Contracts\CheckoutService;
use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\DTO\CheckoutResult;
use Illuminate\Support\Facades\DB;

class TransactionalCheckoutDecorator implements CheckoutService
{
    public function __construct(private CheckoutService $next) {}

    public function checkout(CheckoutCommand $command): CheckoutResult
    {
        return DB::transaction(function () use ($command) {
            return $this->next->checkout($command);
        });
    }
}
