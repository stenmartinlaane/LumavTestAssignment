<?php declare(strict_types=1);

use App\Http\Controllers\EuronicsScrapingController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn() => new Response('OK'));

Route::get('start-scraping', [EuronicsScrapingController::class, 'startScrapingEruronicsData']);
Route::get('get-scraping-result', [EuronicsScrapingController::class, 'getPartialScrapingResults']);
