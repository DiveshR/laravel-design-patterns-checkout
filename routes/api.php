<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/api-pattern-checkout.php';

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
