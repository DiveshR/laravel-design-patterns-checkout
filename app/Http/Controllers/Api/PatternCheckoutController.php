<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\Facades\PatternCheckoutFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatternCheckoutController extends Controller
{
    public function purchase(Request $request, PatternCheckoutFacade $checkout): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'min:1'],
            'product_id' => ['required', 'integer', 'exists:pattern_products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $result = $checkout->purchase(new CheckoutCommand(
            userId: (int) $validated['user_id'],
            productId: (int) $validated['product_id'],
            quantity: (int) $validated['quantity'],
            idempotencyKey: $request->header('Idempotency-Key')
        ));

        return response()->json($result->toArray(), $result->success ? 200 : 422);
    }
}
