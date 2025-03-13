<?php

use Illuminate\Support\Facades\Route;
use Klump\LaravelKlump\Http\Controllers\WebhookController;

Route::post('klump/webhook', [WebhookController::class, 'handle'])
    ->name('klump.webhook');
    