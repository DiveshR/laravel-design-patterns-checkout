<?php

namespace App\Services\PatternCheckout\Facades;

use App\Models\PatternOrder;
use App\Services\PatternCheckout\Contracts\CheckoutService;
use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\DTO\CheckoutResult;
use App\Services\PatternCheckout\Payments\DTO\PaymentRequest;
use App\Services\PatternCheckout\Payments\PaymentGatewayResolver;

class PatternCheckoutFacade
{
    public function __construct(
        private CheckoutService $checkoutService,
        private PaymentGatewayResolver $paymentGatewayResolver,
    ) {}

    public function purchase(CheckoutCommand $command): CheckoutResult
    {
        $checkoutResult = $this->checkoutService->checkout($command);

        if (! $checkoutResult->success || ! $checkoutResult->orderId) {
            return $checkoutResult;
        }

        $order = PatternOrder::query()->findOrFail($checkoutResult->orderId);
        $gateway = $this->paymentGatewayResolver->resolve();

        $payment = $gateway->initialize(new PaymentRequest(
            orderId: $order->id,
            userId: $order->user_id,
            amountInPaise: $order->amount_in_paise,
            currency: 'INR',
            description: 'Pattern checkout order #' . $order->id,
        ));

        $order->update([
            'payment_provider' => $payment->provider,
            'provider_payment_id' => $payment->providerPaymentId,
        ]);

        return $checkoutResult->withPayment($payment->toArray());
    }
}
