<?php

namespace App\Services\PatternCheckout\DTO;

class CheckoutCommand
{
    public function __construct(
        public int $userId,
        public int $productId,
        public int $quantity,
        public ?string $idempotencyKey = null,
    ) {}
}
