# Step-by-Step Guide

## Step 1: Understand the goal

We are not building a large ecommerce app. We are building a small project that clearly explains design patterns through checkout.

The goal is:

```text
Small codebase + strong architecture + interview-level explanation
```

---

## Step 2: Create database tables

We need three tables:

1. `pattern_products`
2. `pattern_orders`
3. `pattern_idempotency_keys`

### Why custom table names?

Because your existing Laravel project may already have `products` or `orders`. The `pattern_` prefix keeps this module safe.

---

## Step 3: Create the core service contract

The contract is:

```php
interface CheckoutService
{
    public function checkout(CheckoutCommand $command): CheckoutResult;
}
```

This means every checkout implementation must follow the same method.

The controller does not depend on `BasicCheckoutService` directly. It depends on the interface.

This is important because Decorators also implement the same interface.

---

## Step 4: Create the core checkout service

`BasicCheckoutService` only creates the order.

It does not handle:

- logging
- idempotency
- transaction
- stock protection
- rate limit

That is intentional.

The core service should do the main business work only.

---

## Step 5: Add Decorator layers

Each decorator has this shape:

```php
class SomeDecorator implements CheckoutService
{
    public function __construct(private CheckoutService $next) {}

    public function checkout(CheckoutCommand $command): CheckoutResult
    {
        // do something before

        $result = $this->next->checkout($command);

        // do something after

        return $result;
    }
}
```

This is the key idea.

One decorator wraps the next decorator.

---

## Step 6: Add Adapter pattern for payment

Your application uses one interface:

```php
interface PaymentGateway
{
    public function initialize(PaymentRequest $request): PaymentResponse;
}
```

Fake Stripe and Fake Razorpay both implement this interface.

The checkout code does not care which gateway is selected.

---

## Step 7: Add Facade pattern

The facade is a simple entry point:

```php
$result = $checkoutFacade->purchase($command);
```

Internally it calls:

- checkout service
- payment gateway resolver
- payment adapter

The controller stays clean.

---

## Step 8: Add Singleton-style container binding

In Laravel, prefer container-managed singleton over classic static singleton.

Good:

```php
$this->app->singleton(PaymentGatewayResolver::class, function ($app) {
    return new PaymentGatewayResolver($app);
});
```

Avoid:

```php
PaymentGatewayResolver::getInstance();
```

Container singleton is easier to test and replace.

---

## Step 9: Run and test

1. Run migrations.
2. Seed demo product.
3. Hit purchase API.
4. Hit same API again with same idempotency key.
5. Confirm no duplicate order was created.
6. Change `.env` payment gateway from `fake_stripe` to `fake_razorpay`.
7. Confirm checkout code does not change.

---

## Step 10: Explain in GitHub README

Your README should clearly explain:

- What problem this solves.
- Which design pattern is used where.
- Why this is better than boolean spaghetti.
- How to run the project.
- How to test idempotency.
- How to extend payment gateways.
