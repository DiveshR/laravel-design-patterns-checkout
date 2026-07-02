<?php

namespace App\Services\PatternCheckout\Payments\DTO;

class PaymentResponse
{
    public function __construct(
        public bool $success,
        public string $provider,
        public ?string $providerPaymentId = null,
        public ?string $paymentUrl = null,
        public ?string $message = null,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'provider' => $this->provider,
            'provider_payment_id' => $this->providerPaymentId,
            'payment_url' => $this->paymentUrl,
            'message' => $this->message,
        ];
    }
}
