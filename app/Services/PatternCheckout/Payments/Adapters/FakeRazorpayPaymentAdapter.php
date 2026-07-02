<?php

namespace App\Services\PatternCheckout\Payments\Adapters;

use App\Services\PatternCheckout\Payments\Contracts\PaymentGateway;
use App\Services\PatternCheckout\Payments\DTO\PaymentRequest;
use App\Services\PatternCheckout\Payments\DTO\PaymentResponse;

class FakeRazorpayPaymentAdapter implements PaymentGateway
{
    public function initialize(PaymentRequest $request): PaymentResponse
    {
        $paymentId = 'fake_razorpay_order_' . $request->orderId . '_' . now()->timestamp;

        return new PaymentResponse(
            success: true,
            provider: 'fake_razorpay',
            providerPaymentId: $paymentId,
            paymentUrl: 'https://example.com/pay/fake-razorpay/' . $paymentId,
            message: 'Fake Razorpay order initialized.'
        );
    }
}
