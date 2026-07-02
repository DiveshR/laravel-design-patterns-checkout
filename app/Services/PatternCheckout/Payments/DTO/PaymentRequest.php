<?php

namespace App\Services\PatternCheckout\Payments\DTO;

class PaymentRequest
{
    public function __construct(
        public int $orderId,
        public int $userId,
        public int $amountInPaise,
        public string $currency = 'INR',
        public string $description = 'Pattern checkout payment',
    ) {}
}
