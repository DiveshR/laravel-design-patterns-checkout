<?php

namespace App\Services\PatternCheckout\DTO;

class CheckoutResult
{
    public function __construct(
        public bool $success,
        public ?int $orderId = null,
        public ?string $message = null,
        public array $payment = [],
    ) {}

    public static function failed(string $message): self
    {
        return new self(success: false, message: $message);
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            success: (bool) ($payload['success'] ?? false),
            orderId: $payload['order_id'] ?? null,
            message: $payload['message'] ?? null,
            payment: $payload['payment'] ?? [],
        );
    }

    public function withPayment(array $payment): self
    {
        return new self(
            success: $this->success,
            orderId: $this->orderId,
            message: 'Order created and payment initialized.',
            payment: $payment,
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'order_id' => $this->orderId,
            'message' => $this->message,
            'payment' => $this->payment,
        ];
    }
}
