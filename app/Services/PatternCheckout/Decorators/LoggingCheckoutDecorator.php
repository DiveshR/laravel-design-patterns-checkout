<?php

namespace App\Services\PatternCheckout\Decorators;

use App\Services\PatternCheckout\Contracts\CheckoutService;
use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\DTO\CheckoutResult;
use Illuminate\Support\Facades\Log;

class LoggingCheckoutDecorator implements CheckoutService
{
    public function __construct(private CheckoutService $next) {}

    public function checkout(CheckoutCommand $command): CheckoutResult
    {
        Log::info('Pattern checkout started', [
            'user_id' => $command->userId,
            'product_id' => $command->productId,
            'quantity' => $command->quantity,
        ]);

        $result = $this->next->checkout($command);

        Log::info('Pattern checkout finished', [
            'success' => $result->success,
            'order_id' => $result->orderId,
            'message' => $result->message,
        ]);

        return $result;
    }
}
