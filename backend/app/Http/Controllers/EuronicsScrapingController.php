<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\Services\Euronics\EuronicsScrapingService;
use Illuminate\Support\Facades\Storage;

final class EuronicsScrapingController extends Controller {


    public function startScrapingEruronicsData(EuronicsScrapingService $euronicsScrapingService) {
        $key = "secret123";
        $euronicsScrapingService->getProductsByCategories($key);

        return response()->isOk();
    }

    public function getPartialScrapingResults()
    {
        $filePath = "productsByCategoryAllData.json";
        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $jsonData = Storage::disk('local')->get($filePath);

        // Decode the JSON data into a PHP array
        $data = json_decode($jsonData, true); // Pass 'true' to get an associative array

        // Check if decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON format'], 500);
        }

        // Return the data as a JSON response
        return response()->json($data, 200);
    }
}
