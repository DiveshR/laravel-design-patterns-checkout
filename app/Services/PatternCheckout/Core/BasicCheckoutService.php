<?php

namespace App\Services\PatternCheckout\Core;

use App\Models\PatternOrder;
use App\Models\PatternProduct;
use App\Services\PatternCheckout\Contracts\CheckoutService;
use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\DTO\CheckoutResult;

class BasicCheckoutService implements CheckoutService
{
    public function checkout(CheckoutCommand $command): CheckoutResult
    {
        $product = PatternProduct::query()->findOrFail($command->productId);

        $order = PatternOrder::query()->create([
            'user_id' => $command->userId,
            'pattern_product_id' => $product->id,
            'quantity' => $command->quantity,
            'amount_in_paise' => $product->price_in_paise * $command->quantity,
            'status' => 'pending_payment',
        ]);

        return new CheckoutResult(
            success: true,
            orderId: $order->id,
            message: 'Order created.'
        );
    }
}
