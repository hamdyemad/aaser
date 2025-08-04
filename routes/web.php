<?php

use App\Http\Mobile\Controllers\TouristAttractionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test', function () {
    return view('receipts.entertainment_activity');
});

Route::get('/make', [TouristAttractionController::class, 'generateArabicPDF']);
