<?php

namespace App\Services\PatternCheckout\Contracts;

use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\DTO\CheckoutResult;

interface CheckoutService
{
    public function checkout(CheckoutCommand $command): CheckoutResult;
}
