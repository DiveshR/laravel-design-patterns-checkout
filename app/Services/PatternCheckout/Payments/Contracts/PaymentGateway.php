<?php

namespace App\Services\PatternCheckout\Payments\Contracts;

use App\Services\PatternCheckout\Payments\DTO\PaymentRequest;
use App\Services\PatternCheckout\Payments\DTO\PaymentResponse;

interface PaymentGateway
{
    public function initialize(PaymentRequest $request): PaymentResponse;
}
