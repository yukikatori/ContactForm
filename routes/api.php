<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ContactController;;

Route::prefix('v1')->group(function () {
    Route::apiResource('contacts', ContactController::class);
});

