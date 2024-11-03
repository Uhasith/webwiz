<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BadRequestException;
use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Service\Admin\HistoryDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HistoryDataController extends Controller
{
    private HistoryDataService $historyDataService;

    /**
     * @param HistoryDataService $historyDataService
     */
    public function __construct(HistoryDataService $historyDataService)
    {
        $this->historyDataService = $historyDataService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function uploadExcel(Request $request): JsonResponse
    {
        $requestData = [
            "equipmentId" => $request->equipment_id,
            "locationId" => $request->location_id
        ];
        Utility::log($requestData, get_class());
        $requestData['file'] = $request->file('file');

        $response = $this->historyDataService->uploadHistoryData($requestData);
        return response()->json($response);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function revertUploadedData(Request $request): JsonResponse
    {
        $requestData = [
            "equipmentId" => $request->equipment_id,
            "locationId" => $request->location_id
        ];
        Utility::log($requestData, get_class());
        $requestData['file'] = $request->file('file');

        $response = $this->historyDataService->revertUploadedData($requestData);
        return response()->json($response);

    }


}
