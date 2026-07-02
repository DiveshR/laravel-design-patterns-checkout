<?php

namespace Tests\Feature;

use App\Models\PatternOrder;
use App\Models\PatternProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatternCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_purchase_product(): void
    {
        PatternProduct::query()->create([
            'name' => 'Test Product',
            'stock' => 3,
            'price_in_paise' => 10000,
        ]);

        $response = $this->postJson('/api/pattern-checkout/purchase', [
            'user_id' => 1,
            'product_id' => 1,
            'quantity' => 1,
        ], [
            'Idempotency-Key' => 'test-key-1',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertDatabaseCount('pattern_orders', 1);
        $this->assertDatabaseHas('pattern_products', [
            'id' => 1,
            'stock' => 2,
        ]);
    }

    public function test_same_idempotency_key_does_not_create_duplicate_order(): void
    {
        PatternProduct::query()->create([
            'name' => 'Test Product',
            'stock' => 3,
            'price_in_paise' => 10000,
        ]);

        for ($i = 0; $i < 2; $i++) {
            $this->postJson('/api/pattern-checkout/purchase', [
                'user_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
            ], [
                'Idempotency-Key' => 'same-key',
            ])->assertOk();
        }

        $this->assertDatabaseCount('pattern_orders', 1);
        $this->assertSame(2, PatternProduct::query()->find(1)->stock);
    }

    public function test_out_of_stock_returns_error(): void
    {
        PatternProduct::query()->create([
            'name' => 'Test Product',
            'stock' => 0,
            'price_in_paise' => 10000,
        ]);

        $response = $this->postJson('/api/pattern-checkout/purchase', [
            'user_id' => 1,
            'product_id' => 1,
            'quantity' => 1,
        ], [
            'Idempotency-Key' => 'out-of-stock-key',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        $this->assertDatabaseCount('pattern_orders', 0);
    }
}
