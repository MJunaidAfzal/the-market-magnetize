<?php

use App\Http\Controllers\DownloadExcelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleSheetApiController;

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/download-users-excel', [DownloadExcelController::class, 'download'])->name('download.users.excel');

Route::get('sheet', [GoogleSheetApiController::class, 'index']);
Route::get('sheet/random', [GoogleSheetApiController::class, 'storeRandomData']);

// Signed URL route for profile photos
Route::get('/signed-image/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    return response()->file($filePath);
})->name('image.sign')->middleware('signed');
