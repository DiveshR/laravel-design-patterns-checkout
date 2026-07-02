<?php

namespace App\Services\PatternCheckout\Decorators;

use App\Services\PatternCheckout\Contracts\CheckoutService;
use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\DTO\CheckoutResult;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitedCheckoutDecorator implements CheckoutService
{
    public function __construct(private CheckoutService $next) {}

    public function checkout(CheckoutCommand $command): CheckoutResult
    {
        $key = 'pattern-checkout:user:' . $command->userId;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return CheckoutResult::failed('Too many checkout attempts. Please try again after one minute.');
        }

        RateLimiter::hit($key, 60);

        return $this->next->checkout($command);
    }
}
