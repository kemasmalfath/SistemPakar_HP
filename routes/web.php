<?php

use App\Http\Controllers\ExpertSystemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/expert-system', [ExpertSystemController::class, 'index'])->name('expert.system');
Route::post('/expert-system/process', [ExpertSystemController::class, 'processRecommendation'])->name('expert.system.process');