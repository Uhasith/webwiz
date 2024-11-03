<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\ModelsV2\HourlySensorData;
use App\Repository\Admin\SensorRepo;
use App\Service\Admin\HistoryDataService;
use App\Service\User\SensorsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class HistoricalDataController extends Controller
{
    /**
     * @var SensorsService
     * @var SensorRepo
     */
    private SensorsService $sensorsService;
    protected SensorRepo $sensorRepo;
    private HistoryDataService $historyDataService;

    /**
     * @param SensorsService $sensorsService
     * @param SensorRepo $sensorRepo
     * @param HistoryDataService $historyDataService
     */
    public function __construct(
        SensorsService  $sensorsService,
        SensorRepo $sensorRepo,HistoryDataService $historyDataService
    ) {
        $this->sensorsService =  $sensorsService;
        $this->sensorRepo = $sensorRepo;
        $this->historyDataService = $historyDataService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): \Inertia\Response
    {
        $equipmentTypes = $this->sensorsService->homepageService();
        $sensors = $this->sensorsService->getSensorsByEquipmentId('all');

        return Inertia::render('Adminview/HistoricalData', [
            'equipmentTypes' => $equipmentTypes,
            'sensors' => $sensors
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getSensorDetails(Request $request): \Illuminate\Http\JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $data = [
            'searchTerm' => $request->get('typeFilteredProvince') !== "All" ? $request->get('typeFilteredProvince') : $request->get('search'),
            'start' => $request->get('startDate'),
            'end' => $request->get('endDate'),
            'selectedEquipmentType' => $request->get('selectedEquipmentType') === "all" ? '' : $request->get('selectedEquipmentType'),
            'selectedSensors' => $request->get('selectsensors') === "all" ? '' : $request->get('selectsensors'),
            'selectedStatus' => $request->get('selectedStatus'),
            'selectedDate' => $request->get('selectedDate'),
        ];

        $query = $this->historyDataService->getAll($data, Auth::user());
        $lastUpdated = $this->historyDataService->lastUpdatedRecord(Utility::$hour);
        $totalRecords = $query->count();
        $numberOfPages = ceil($totalRecords / $perPage);

        $sensorData = $query
            ->select(['id', 'created_at as date_time', 'sensor_location_id', 'pm2_5', 'pm10', 'o3', 'co', 'no2', 'so2', 'co2', 'aqi', 'status'])
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $dataTable = DataTables::of($sensorData)
            ->addColumn('pm2_5', fn($row) => $row->pm2_5 ?? '')
            ->addColumn('pm10', fn($row) => $row->pm10 ?? '')
            ->addColumn('o3', fn($row) => $row->o3 ?? '')
            ->addColumn('co', fn($row) => $row->co ?? '')
            ->addColumn('no2', fn($row) => $row->no2 ?? '')
            ->addColumn('so2', fn($row) => $row->so2 ?? '')
            ->addColumn('co2', fn($row) => $row->co2 ?? '')
            ->addColumn('temperature', fn($row) => round($row->weatherRecords->temperature,2) ?? '')
            ->addColumn('pressure', fn($row) => round($row->weatherRecords->pressure,2) ?? '')
            ->addColumn('wind', fn($row) => round($row->weatherRecords->wind,2) ?? '')
            ->addColumn('humidity', fn($row) => round($row->weatherRecords->humidity,2) ?? '')
            ->addColumn('precipitation', fn($row) => round($row->weatherRecords->precipitation,2) ?? '')
            ->make(true);

        $response = $dataTable->getData(true);
        $response['numberOfPages'] = $numberOfPages;
        $response['selectsensors'] = $data['selectedSensors'];
        $response['lastUpdatedDate'] = $lastUpdated;
        $response['manuallyAddedHourlySensorDataCount'] = $totalRecords;
        $response['selectedEquipmentType'] = $data['selectedEquipmentType'];

        return response()->json($response);
    }


}
