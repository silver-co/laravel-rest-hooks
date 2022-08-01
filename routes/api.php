<?php

use Illuminate\Support\Facades\Route;
use SilverCO\RestHooks\Http\Controllers\RestHookController;

Route::apiResource('hooks', RestHookController::class);
