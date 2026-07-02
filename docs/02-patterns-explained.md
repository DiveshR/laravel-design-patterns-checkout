# Patterns Explained in This Project

## 1. Decorator Pattern

### Problem

Checkout needs many extra behaviors:

- logging
- rate limit
- idempotency
- database transaction
- stock protection

If we put all of this in one service, it becomes messy.

### Solution

Keep the core checkout clean and wrap it with decorators.

```text
Logging
  ↓
Rate limit
  ↓
Idempotency
  ↓
Transaction
  ↓
Stock protection
  ↓
Basic checkout
```

### Where to see it

```text
app/Services/PatternCheckout/Decorators
```

---

## 2. Adapter Pattern

### Problem

Every payment provider has a different API.

Stripe, Razorpay, Cashfree, PayPal, all behave differently.

### Solution

Create one interface for your app:

```php
interface PaymentGateway
{
    public function initialize(PaymentRequest $request): PaymentResponse;
}
```

Then create adapters:

```text
FakeStripePaymentAdapter
FakeRazorpayPaymentAdapter
```

### Where to see it

```text
app/Services/PatternCheckout/Payments
```

---

## 3. Facade Pattern

### Problem

Controller should not know the full internal checkout workflow.

### Solution

Create one simple entry point:

```php
$facade->purchase($command);
```

The facade hides checkout + payment complexity.

### Where to see it

```text
app/Services/PatternCheckout/Facades/PatternCheckoutFacade.php
```

---

## 4. Singleton Pattern

### Problem

Some infrastructure services should be reused.

### Solution

Use Laravel's container singleton binding for stateless services.

### Where to see it

```text
app/Providers/PatternCheckoutServiceProvider.php
```

### Important warning

Do not use singleton for current user, cart state, checkout state, or request-specific business data.

Safe singleton:

```text
PaymentGatewayResolver
CurrencyFormatter
Config reader
Client factory
```

Dangerous singleton:

```text
CurrentUser
CurrentCart
CheckoutContext
TenantState if not request-scoped
```
