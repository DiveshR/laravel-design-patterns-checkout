<?php

namespace App\Services\PatternCheckout\Decorators;

use App\Models\PatternIdempotencyKey;
use App\Services\PatternCheckout\Contracts\CheckoutService;
use App\Services\PatternCheckout\DTO\CheckoutCommand;
use App\Services\PatternCheckout\DTO\CheckoutResult;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class IdempotentCheckoutDecorator implements CheckoutService
{
    public function __construct(private CheckoutService $next) {}

    public function checkout(CheckoutCommand $command): CheckoutResult
    {
        if (! $command->idempotencyKey) {
            return CheckoutResult::failed('Idempotency-Key header is required.');
        }

        $requestHash = hash('sha256', json_encode([
            'user_id' => $command->userId,
            'product_id' => $command->productId,
            'quantity' => $command->quantity,
        ]));

        return DB::transaction(function () use ($command, $requestHash) {
            $record = PatternIdempotencyKey::query()
                ->where('user_id', $command->userId)
                ->where('key', $command->idempotencyKey)
                ->lockForUpdate()
                ->first();

            if ($record && $record->status === 'completed') {
                return CheckoutResult::fromArray($record->response_payload ?? []);
            }

            if ($record && $record->request_hash !== $requestHash) {
                return CheckoutResult::failed('Same idempotency key used with different request data.');
            }

            if (! $record) {
                try {
                    $record = PatternIdempotencyKey::query()->create([
                        'user_id' => $command->userId,
                        'key' => $command->idempotencyKey,
                        'request_hash' => $requestHash,
                        'status' => 'processing',
                    ]);
                } catch (QueryException) {
                    $record = PatternIdempotencyKey::query()
                        ->where('user_id', $command->userId)
                        ->where('key', $command->idempotencyKey)
                        ->lockForUpdate()
                        ->firstOrFail();
                }
            }

            $result = $this->next->checkout($command);

            $record->update([
                'status' => 'completed',
                'response_payload' => $result->toArray(),
            ]);

            return $result;
        });
    }
}
