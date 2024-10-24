<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Service\Admin\AnalyticsService;
use App\Service\User\SensorsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    private AnalyticsService $analyticsService;
    private SensorsService $sensorsService;

    /**
     * @param AnalyticsService $analyticsService
     * @param SensorsService $sensorsService
     */
    public function __construct(AnalyticsService $analyticsService,SensorsService $sensorsService)
    {
        $this->analyticsService = $analyticsService;
        $this->sensorsService = $sensorsService;
    }

    public function index()
    {
        Utility::log([], get_class());
        $sensors = $this->sensorsService->getSensorsByEquipmentId('all');

        return Inertia::render(null, [
            'sensors' => $sensors
        ]);
    }

    public function getAqiTrend(Request $request): \Illuminate\Http\JsonResponse
    {
        $requestData = [
            "equipmentId" => $request->equipment_id,
            "locationId" => $request->location_id,
            "period" => $request->period,
        ];
        Utility::log($requestData, get_class());
        $response = $this->analyticsService->getAqiTrend($requestData);

        return response()->json([$response]);

    }

    public function getWeatherTrend(Request $request): \Illuminate\Http\JsonResponse
    {
        $requestData = [
            "equipmentId" => $request->equipment_id,
            "parameter" => $request->parameter,
            "locationId" => $request->location_id,
            "period" => $request->period,
        ];
        Utility::log($requestData, get_class());
        $response = $this->analyticsService->getWeatherTrend($requestData);
        return response()->json([$response]);

    }

    public function getTop10Cities(Request $request): \Illuminate\Http\JsonResponse
    {
        $requestData = [
            "equipmentId" => $request->equipment_id,
            "ranking" => $request->ranking,
            "period" => $request->period,
        ];
        Utility::log($requestData, get_class());
        $response = $this->analyticsService->getTop10Cities($requestData);

        return response()->json($response);
    }

    public function getDailyAqiTrendByMonth(Request $request): \Illuminate\Http\JsonResponse
    {
        $requestData = [
            "equipmentId" => $request->equipment_id,
            "locationId" => $request->location_id,
            "year" => $request->year,
            "month" => $request->month
        ];
        Utility::log($requestData, get_class());
        $response = $this->analyticsService->getDailyAqiTrendByMonth($requestData);

        return response()->json([$response]);
    }


}
