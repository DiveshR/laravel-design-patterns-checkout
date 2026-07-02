<?php

namespace App\Services\PatternCheckout\Payments;

use App\Services\PatternCheckout\Payments\Adapters\FakeRazorpayPaymentAdapter;
use App\Services\PatternCheckout\Payments\Adapters\FakeStripePaymentAdapter;
use App\Services\PatternCheckout\Payments\Contracts\PaymentGateway;
use InvalidArgumentException;

class PaymentGatewayResolver
{
    public function resolve(?string $gateway = null): PaymentGateway
    {
        $gateway = $gateway ?: config('pattern_checkout.payment_gateway', 'fake_stripe');

        return match ($gateway) {
            'fake_stripe' => app(FakeStripePaymentAdapter::class),
            'fake_razorpay' => app(FakeRazorpayPaymentAdapter::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway [{$gateway}]."),
        };
    }
}
