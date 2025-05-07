<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhoIsController;

Route::get('/', [WhoIsController::class, 'form']);

