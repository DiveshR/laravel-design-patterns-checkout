# Interview Script

## 30-second version

I built a mini checkout system to demonstrate Decorator, Adapter, Facade, and Singleton patterns in Laravel. The controller calls a facade. The facade coordinates checkout and payment. The checkout service is wrapped with decorators for logging, rate limiting, idempotency, transactions, and stock protection. Payment providers are adapters behind one `PaymentGateway` interface. I use Laravel's service container singleton for stateless resolver services instead of classic static singletons.

---

## 2-minute version

The main problem I wanted to solve was boolean spaghetti. In checkout flows, we often add feature flags like logging, idempotency, transaction, rate limit, stock lock, and payment provider selection. If these are scattered across controllers and services, the system becomes hard to test and change.

So I created a `CheckoutService` interface. The real service, `BasicCheckoutService`, only creates the order. Then I wrapped it with decorator layers. Each decorator owns one responsibility. For example, `IdempotentCheckoutDecorator` prevents duplicate order creation, `TransactionalCheckoutDecorator` wraps the operation in a DB transaction, and `StockProtectedCheckoutDecorator` performs atomic stock deduction.

For payments, I used the Adapter pattern. My app talks to a stable `PaymentGateway` interface. Fake Stripe and Fake Razorpay adapters translate provider-specific behavior into my app's standard response.

I used a Facade class as a clean use-case entry point. The controller only calls `purchase()`, while the facade coordinates checkout and payment internally.

For Singleton, I avoided classic static singletons. I used Laravel's service container singleton for stateless infrastructure like the payment resolver, which is safer and testable.

---

## Best one-line explanation

This project shows how to replace checkout boolean spaghetti with clean runtime composition using Decorators, Adapters, a Facade entry point, and container-managed Singleton bindings.
