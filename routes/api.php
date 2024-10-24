<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\User\DashboardController;

if (config('app.https_enabled', false)) {
    URL::forceScheme("https");
}
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('test')->middleware(['check.testing'])->group(function () {
    Route::get('/analytics/top-10', [\App\Http\Controllers\Admin\AnalyticsController::class, 'getTop10Cities']);
    Route::get('/iqAir', [DashboardController::class, 'iqAirSave']);
    Route::post('/weather-data/{sensorLocationId}', [DashboardController::class, 'saveTempWeather']);
    Route::post('/sendEmail', [DashboardController::class, 'sendEmail']);
    Route::post('/send-multiple-notifications',[DashboardController::class,'sendNotificationJobTest']);
    Route::post('/send-notification',[DashboardController::class,'sendNotification']);
    Route::post('/notifications', [DashboardController::class, 'saveNotification']);
     Route::post('/wqd/extract', function (){
      return (new \App\Imports\Extract\SplitTextExport())->collection();
    });
});

Route::post('/history_data/upload', [\App\Http\Controllers\Admin\HistoryDataController::class, 'uploadExcel']);
Route::post('/history_data/revert', [\App\Http\Controllers\Admin\HistoryDataController::class, 'revertUploadedData']);

