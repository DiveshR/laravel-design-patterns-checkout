<?php

return [
    'logging' => env('PATTERN_CHECKOUT_LOGGING', true),
    'rate_limit' => env('PATTERN_CHECKOUT_RATE_LIMIT', true),
    'idempotency' => env('PATTERN_CHECKOUT_IDEMPOTENCY', true),
    'transaction' => env('PATTERN_CHECKOUT_TRANSACTION', true),
    'stock_protection' => env('PATTERN_CHECKOUT_STOCK_PROTECTION', true),

    'payment_gateway' => env('PATTERN_PAYMENT_GATEWAY', 'fake_stripe'),
];
