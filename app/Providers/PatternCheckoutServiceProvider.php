<?php

namespace App\Providers;

use App\Services\PatternCheckout\Contracts\CheckoutService;
use App\Services\PatternCheckout\Core\BasicCheckoutService;
use App\Services\PatternCheckout\Decorators\IdempotentCheckoutDecorator;
use App\Services\PatternCheckout\Decorators\LoggingCheckoutDecorator;
use App\Services\PatternCheckout\Decorators\RateLimitedCheckoutDecorator;
use App\Services\PatternCheckout\Decorators\StockProtectedCheckoutDecorator;
use App\Services\PatternCheckout\Decorators\TransactionalCheckoutDecorator;
use App\Services\PatternCheckout\Inventory\Contracts\StockReserver;
use App\Services\PatternCheckout\Inventory\MySqlStockReserver;
use App\Services\PatternCheckout\Payments\PaymentGatewayResolver;
use Illuminate\Support\ServiceProvider;

class PatternCheckoutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/pattern_checkout.php',
            'pattern_checkout'
        );

        $this->app->bind(StockReserver::class, MySqlStockReserver::class);

        // Singleton pattern, Laravel way: a stateless infrastructure resolver shared by the container.
        $this->app->singleton(PaymentGatewayResolver::class, function () {
            return new PaymentGatewayResolver();
        });

        // Runtime layer: decide which decorators are active from config.
        $this->app->bind(CheckoutService::class, function ($app) {
            $service = new BasicCheckoutService();

            if (config('pattern_checkout.stock_protection')) {
                $service = new StockProtectedCheckoutDecorator(
                    next: $service,
                    stockReserver: $app->make(StockReserver::class)
                );
            }

            if (config('pattern_checkout.transaction')) {
                $service = new TransactionalCheckoutDecorator($service);
            }

            if (config('pattern_checkout.idempotency')) {
                $service = new IdempotentCheckoutDecorator($service);
            }

            if (config('pattern_checkout.rate_limit')) {
                $service = new RateLimitedCheckoutDecorator($service);
            }

            if (config('pattern_checkout.logging')) {
                $service = new LoggingCheckoutDecorator($service);
            }

            return $service;
        });
    }
}
