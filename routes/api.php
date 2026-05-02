<?php
use App\Http\Controllers\Api\TestController;

Route::get('/test', [TestController::class, 'index'])
    ->name('test-route');
