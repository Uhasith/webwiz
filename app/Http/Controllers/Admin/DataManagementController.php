<?php

namespace App\Http\Controllers\Admin;

use App\AqiService;
use App\Helpers\Utility;
use App\ModelsV2\HourlySensorData;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\ModelsV2\SensorDatas;
use App\Exports\SensorDataExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repository\Admin\SensorRepo;
use App\Service\User\SensorsService;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Admin\SensorDataUpdateRequest;

class DataManagementController extends Controller
{
    private $sensorsService;
    protected $sensorRepo;

    public function __construct(
        SensorsService  $sensorsService,
        SensorRepo $sensorRepo
    ) {
        $this->sensorsService =  $sensorsService;
        $this->sensorRepo = $sensorRepo;
    }
    public function index(Request $request)
    {
        $equipmentTypes = $this->sensorsService->homepageService();
        $sensors = $this->sensorsService->getSensorsByEquipmentId('all');

        return Inertia::render('Adminview/DataManagement', [
            'equipmentTypes' => $equipmentTypes,
            'sensors' => $sensors
        ]);
    }

    public function getSensorData()
    {
        $data = $this->sensorRepo->getAllSensorData();
        return response()->json($data);
    }

    public function updateSensorData(SensorDataUpdateRequest $request)
    {
        $data = $request->all();
        return $this->sensorsService->updateSensorData($data);
    }

    public function updateSensorDataStatus(Request $request)
    {
        $data = $request->all();
        return $this->sensorsService->updateSensorDataStatus($data);
    }

    public function edit($id)
    {
        $sensor = SensorDatas::with('sensorLocation.location.district', 'sensorLocation.sensor.equipmentType')->findOrFail($id);
        return response()->json($sensor);
    }

    public function delete($id)
    {
        $data = $this->sensorRepo->findSensorData($id);
        $data->delete();

        return response()->json(['message' => 'Data deleted successfully'], 200);
    }
    public function getLocationDetail($e_type, $m_type)
    {
        if ($e_type != "all" && $m_type != "all") {
            $data = DB::table('locations')
                ->select(
                    'locations.id',
                    'locations.name',
                )
                ->join('sensor_locations', 'locations.id', '=', 'sensor_locations.location_id')
                ->join('sensors', 'sensor_locations.sensor_id', '=', 'sensors.id')
                ->Where('sensors.equipment_type_id', '=', "$e_type")
                ->Where('sensors.id', '=', "$m_type")
                ->get();
            return response()->json($data);
        }
        if($m_type != "all"){
            $data = DB::table('locations')
                ->select(
                    'locations.id',
                    'locations.name',
                )
                ->join('sensor_locations', 'locations.id', '=', 'sensor_locations.location_id')
                ->join('sensors', 'sensor_locations.sensor_id', '=', 'sensors.id')
                ->Where('sensors.id', '=', "$m_type")
                ->get();
            return response()->json($data);
        }
        if($e_type != "all"){
            $data = DB::table('locations')
                ->select(
                    'locations.id',
                    'locations.name',
                )
                ->join('sensor_locations', 'locations.id', '=', 'sensor_locations.location_id')
                ->join('sensors', 'sensor_locations.sensor_id', '=', 'sensors.id')
                ->Where('sensors.equipment_type_id', '=', "$e_type")
                ->get();
            return response()->json($data);
        }
    }
    public function getSensorDetails(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $searchTerm = $request->get('search');
        $selectedDate = $request->get('selectedDate');
        $start = $request->get('startDate');
        $end = $request->get('endDate');
        $selectedStatus = $request->get('selectedStatus');
        $selectedEquipmentType = $request->get('selectedEquipmentType', '');
        $selectsensors = $request->get('selectsensors', '');
        $selectProvince = $request->get('typeFilteredProvince');

        // Handle the "all" cases
        if ($selectedEquipmentType === "all") {
            $selectedEquipmentType = '';
        }
        if ($selectsensors === "all") {
            $selectsensors = '';
        }
        if ($selectProvince !== "All") {
            $searchTerm = $selectProvince;
        }

        // Get the current authenticated user
        $user = Auth::user();

        // Start the base query
        $query = DB::table('sensor_datas')
            ->whereNull('sensor_datas.deleted_at')
            ->join('sensor_locations', 'sensor_datas.sensor_location_id', '=', 'sensor_locations.id')
            ->join('weather_records', 'sensor_datas.id', '=', 'weather_records.sensor_data_id')
            ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
            ->join('sensors', 'sensor_locations.sensor_id', '=', 'sensors.id');


        if (!$user->hasRole('superadmin')) {
            $organizationIds = $user->userOrganizations()->pluck('organization_id')->toArray();
            $query->whereIn('sensor_locations.organization_id', $organizationIds);
        }

        // Apply the date range filter if both $start and $end are provided
        if ($start && $end) {
            $query->whereBetween('sensor_datas.created_at', [$start, $end]);
        }

        // Apply conditional filters based on provided inputs
        $query->where(function ($query) use ($searchTerm, $selectsensors, $selectedEquipmentType, $selectedDate, $selectedStatus) {
            if (!empty($searchTerm)) {
                $query->where('locations.name', 'like', "%{$searchTerm}%");
            }
            if (!empty($selectedEquipmentType)) {
                $query->where('sensors.equipment_type_id', '=', $selectedEquipmentType);
            }
            if (!empty($selectsensors)) {
                $query->where('sensors.id', '=', $selectsensors);
            }
            if (!empty($selectedDate)) {
                $query->where('sensor_datas.created_at', 'like', "%{$selectedDate}%");
            }
            if (!empty($selectedStatus) && $selectedStatus !== 'ALL') {
                $query->where('sensor_datas.status', '=', $selectedStatus);
            }
        });

        // Get the total number of records
        $totalRecords = $query->count();

        // Calculate total number of pages
        $numberOfPages = ceil($totalRecords / $perPage);

        // Retrieve the paginated data
        $data = $query
            ->select(
                'sensor_datas.id as id',
                'sensor_datas.created_at as date_time',
                'sensor_locations.location_id as location_id',
                'locations.name as location',
                'sensor_datas.pm2_5',
                'sensor_datas.pm10',
                'sensor_datas.o3',
                'sensor_datas.co',
                'sensor_datas.no2',
                'sensor_datas.so2',
                'sensor_datas.co2',
                'sensor_datas.aqi',
                'sensor_datas.status',
                'sensors.equipment_type_id',
                'temperature',
                'pressure',
                'wind',
                'humidity',
                'precipitation',
            )
            ->orderBy('sensor_datas.created_at', 'desc') // Order by latest records
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Format the response for DataTables
        $dataTable = DataTables::of($data)
            ->addColumn('pm2_5', fn($data) => $data->pm2_5 ?? '')
            ->addColumn('pm10', fn($data) => $data->pm10 ?? '')
            ->addColumn('o3', fn($data) => $data->o3 ?? '')
            ->addColumn('co', fn($data) => $data->co ?? '')
            ->addColumn('no2', fn($data) => $data->no2 ?? '')
            ->addColumn('so2', fn($data) => $data->so2 ?? '')
            ->addColumn('co2', fn($data) => $data->co2 ?? '')
            ->addColumn('temperature', fn($data) =>  $data->temperature ?? '')
            ->addColumn('pressure', fn($data) =>   $data->pressure ?? '')
            ->addColumn('wind', fn($data) =>   $data->wind ?? '')
            ->addColumn('humidity', fn($data) =>   $data->humidity ?? '')
            ->addColumn('precipitation', fn($data) =>   $data->precipitation ?? '')
            ->make(true);

        // Add numberOfPages to the response
        $response = $dataTable->getData(true);
        $response['numberOfPages'] = $numberOfPages;
        $response['selectsensors'] = $selectsensors;
        $response['filteredDataCount'] = $totalRecords;
        $response['lastUpdatedDate'] = SensorDatas::where('status',Utility::$statusActive)->latest('created_at')
            ->value('created_at');
        $response['selectedEquipmentType'] = $selectedEquipmentType;

        return response()->json($response);
    }


    public function exportFilteredSensorsData(Request $request)
    {
        return Excel::download(new SensorDataExport($request), 'Sensors-data.xlsx');
    }
}
