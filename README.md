# Laravel Design Patterns Mini Project: Pattern Checkout

A small Laravel module to learn and demonstrate four important design patterns with a real-world checkout flow:

- **Decorator**: add behavior around checkout without changing the core service.
- **Adapter**: normalize Stripe / Razorpay style payment APIs behind one app interface.
- **Facade**: expose one simple `purchase()` method that hides the internal workflow.
- **Singleton**: use Laravel container singleton for stateless infrastructure/resolver services.

This project is intentionally small, but the structure is senior-level and interview-friendly.

---

## 1. What you are building

A mini flash-sale checkout API:

```text
POST /api/pattern-checkout/purchase
```

It will:

1. Validate a product and quantity.
2. Prevent duplicate order creation using an idempotency key.
3. Deduct stock safely using an atomic database update.
4. Create an order.
5. Start a fake payment using a selected payment adapter.
6. Log the checkout flow.
7. Rate limit checkout attempts.

---

## 2. Why this project matters

This module turns design patterns into real Laravel code.

| Pattern | Where used | Why |
|---|---|---|
| Decorator | `Decorators/*CheckoutDecorator.php` | Add logging, rate limit, idempotency, transaction, and stock layers around checkout |
| Adapter | `Payments/Adapters/*PaymentAdapter.php` | Make different payment providers look the same to your app |
| Facade | `Facades/PatternCheckoutFacade.php` | Give controller one simple entry point |
| Singleton | `PatternCheckoutServiceProvider.php` | Reuse stateless resolver via Laravel's service container |

---

## 3. Install into a fresh Laravel project

Copy the folders from this module into your Laravel project root:

```text
app/
config/
database/
routes/
tests/
```

Then register the provider.

For Laravel 11+ style apps, open:

```text
bootstrap/providers.php
```

Add:

```php
App\Providers\PatternCheckoutServiceProvider::class,
```

Example:

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\PatternCheckoutServiceProvider::class,
];
```

Then add the route file to `routes/api.php`:

```php
require __DIR__.'/api-pattern-checkout.php';
```

If your project does not have `routes/api.php`, create it or install Laravel API scaffolding.

---

## 4. Add config values

Add these to `.env`:

```env
PATTERN_CHECKOUT_LOGGING=true
PATTERN_CHECKOUT_RATE_LIMIT=true
PATTERN_CHECKOUT_IDEMPOTENCY=true
PATTERN_CHECKOUT_TRANSACTION=true
PATTERN_CHECKOUT_STOCK_PROTECTION=true
PATTERN_PAYMENT_GATEWAY=fake_stripe
```

Available fake gateways:

```env
PATTERN_PAYMENT_GATEWAY=fake_stripe
PATTERN_PAYMENT_GATEWAY=fake_razorpay
```

---

## 5. Run migrations and seed product

```bash
php artisan migrate
php artisan db:seed --class=PatternCheckoutSeeder
```

The seeder creates one demo product:

```text
Name: Interview Flash Sale Product
Stock: 10
Price: ₹999.00
```

---

## 6. Test with cURL

You need an authenticated user if your API route uses `auth:sanctum`. For first learning, the route is intentionally public and accepts `user_id` in the request body.

```bash
curl -X POST http://127.0.0.1:8000/api/pattern-checkout/purchase \
  -H "Content-Type: application/json" \
  -H "Idempotency-Key: demo-key-001" \
  -d '{
    "user_id": 1,
    "product_id": 1,
    "quantity": 1
  }'
```

Expected response:

```json
{
  "success": true,
  "order_id": 1,
  "message": "Order created and payment initialized.",
  "payment": {
    "success": true,
    "provider": "fake_stripe",
    "provider_payment_id": "fake_stripe_pi_...",
    "payment_url": "https://example.com/pay/fake-stripe/...",
    "message": "Fake Stripe payment initialized."
  }
}
```

Now run the same request again with the same `Idempotency-Key`.

You should get the same saved response instead of a duplicate order.

---

## 7. Architecture diagram

```text
HTTP Request
    ↓
PatternCheckoutController
    ↓
PatternCheckoutFacade
    ↓
CheckoutService interface
    ↓
LoggingCheckoutDecorator
    ↓
RateLimitedCheckoutDecorator
    ↓
IdempotentCheckoutDecorator
    ↓
TransactionalCheckoutDecorator
    ↓
StockProtectedCheckoutDecorator
    ↓
BasicCheckoutService
    ↓
PaymentGateway interface
    ↓
FakeStripePaymentAdapter / FakeRazorpayPaymentAdapter
```

---

## 8. Interview explanation

> I built a mini checkout module where the controller depends on a facade instead of knowing all internal details. The facade coordinates checkout and payment. The checkout service is built through decorator layers, so logging, rate limiting, idempotency, transactions, and stock protection stay outside the core order creation logic. Payment providers are implemented using adapters behind one `PaymentGateway` interface. The payment resolver is bound as a container singleton because it is stateless infrastructure. This avoids boolean spaghetti and makes the system easier to test, extend, and explain.

---

## 9. Learning order

Study files in this order:

1. `app/Services/PatternCheckout/Contracts/CheckoutService.php`
2. `app/Services/PatternCheckout/Core/BasicCheckoutService.php`
3. `app/Services/PatternCheckout/Decorators/LoggingCheckoutDecorator.php`
4. `app/Services/PatternCheckout/Decorators/TransactionalCheckoutDecorator.php`
5. `app/Services/PatternCheckout/Decorators/StockProtectedCheckoutDecorator.php`
6. `app/Services/PatternCheckout/Decorators/IdempotentCheckoutDecorator.php`
7. `app/Services/PatternCheckout/Payments/Contracts/PaymentGateway.php`
8. `app/Services/PatternCheckout/Payments/Adapters/FakeStripePaymentAdapter.php`
9. `app/Services/PatternCheckout/Facades/PatternCheckoutFacade.php`
10. `app/Providers/PatternCheckoutServiceProvider.php`

---

## 10. Next improvements

After this works, improve it with:

- Real Sanctum authentication.
- Real Stripe/Razorpay SDK integration.
- Redis stock reservation.
- Queue-based order notification.
- Webhook handler with payment adapter normalization.
- Feature tests for duplicate requests and out-of-stock cases.
# laravel-design-patterns-checkout
