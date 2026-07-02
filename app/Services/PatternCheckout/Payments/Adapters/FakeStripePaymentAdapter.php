<?php

namespace App\Services\PatternCheckout\Payments\Adapters;

use App\Services\PatternCheckout\Payments\Contracts\PaymentGateway;
use App\Services\PatternCheckout\Payments\DTO\PaymentRequest;
use App\Services\PatternCheckout\Payments\DTO\PaymentResponse;

class FakeStripePaymentAdapter implements PaymentGateway
{
    public function initialize(PaymentRequest $request): PaymentResponse
    {
        $paymentId = 'fake_stripe_pi_' . $request->orderId . '_' . now()->timestamp;

        return new PaymentResponse(
            success: true,
            provider: 'fake_stripe',
            providerPaymentId: $paymentId,
            paymentUrl: 'https://example.com/pay/fake-stripe/' . $paymentId,
            message: 'Fake Stripe payment initialized.'
        );
    }
}
