<?php

use App\Http\Controllers\Api\PatternCheckoutController;
use Illuminate\Support\Facades\Route;

Route::prefix('pattern-checkout')->group(function () {
    Route::post('/purchase', [PatternCheckoutController::class, 'purchase']);
});
