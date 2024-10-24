<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Utility;
use Inertia\Inertia;
use App\Service\Admin\ReportTemplateService;
use Google\Cloud\Core\Exception\BadRequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Repository\Admin\TemplateRepo;
use Illuminate\Support\Facades\DB;
use App\Service\User\LocationsService;
use App\Service\User\SensorsService;
use App\Repository\Admin\SensorRepo;
use App\ModelsV2\Sensors;
use App\ModelsV2\ReportTemplate;
use App\ModelsV2\Reports;

class ReportTemplateController
{
    private ReportTemplateService $reportTemplateService;
    private  $templateRepo;

    private $sensorsService;
    private $locationsService;
    protected $sensorRepo;



    public function __construct(ReportTemplateService $reportTemplateService, TemplateRepo $templateRepo,SensorsService  $sensorsService,
    LocationsService $locationsService,
    SensorRepo $sensorRepo)
    {
        $this->reportTemplateService = $reportTemplateService;
        $this->templateRepo = $templateRepo;
        $this->sensorsService =  $sensorsService;
        $this->locationsService = $locationsService;
        $this->sensorRepo = $sensorRepo;
    }
    public function index(Request $request)
    {
        $equipmentTypes = $this->sensorsService->homepageService();
        $sensors = $this->sensorsService->getSensorsByEquipmentId('all');

        return Inertia::render('Adminview/CreateReport', [
            'equipmentTypes' => $equipmentTypes,
            'sensors' => $sensors
        ]);
    }
    public function getEquipmentsData(Request $request)
    {
        $selectedEquipmentType = $request->get('selectedEquipmentType');
        $selectsensors = $request->get('selectsensors');
        $selectProvince = $request->get('typeFilteredProvince');

        if ($selectedEquipmentType == "all") {
            $selectedEquipmentType = "";
        }
        if ($selectsensors == "all") {
            $selectsensors = "";
        }
        if ($selectProvince == "All") {
            $selectProvince = "";
        }
        if (!empty($selectedEquipmentType) && !empty($selectsensors)) {
        $query = Sensors::select('sensors.id','sensors.name','sensors.equipment_type_id','locations.id as location_id', 'equipment_types.type_name', 'locations.name as location_name')
        ->join('equipment_types', 'sensors.equipment_type_id', '=', 'equipment_types.id')
        ->join('sensor_locations', 'sensors.id', '=', 'sensor_locations.sensor_id')
        ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
        ->where('sensors.id', '=', "$selectsensors")
        ->Where('sensors.equipment_type_id', '=', "$selectedEquipmentType")
        ->Where('locations.name', 'like', "%{$selectProvince}%")
        ->get();
        }elseif(!empty($selectedEquipmentType)){
            $query = Sensors::select('sensors.id','sensors.name', 'sensors.equipment_type_id','locations.id as location_id',  'equipment_types.type_name', 'locations.name as location_name')
        ->join('equipment_types', 'sensors.equipment_type_id', '=', 'equipment_types.id')
        ->join('sensor_locations', 'sensors.id', '=', 'sensor_locations.sensor_id')
        ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
        ->Where('sensors.equipment_type_id', '=', "$selectedEquipmentType")
        ->Where('locations.name', 'like', "%{$selectProvince}%")
        ->get();
        }elseif(!empty($selectsensors)){
            $query = Sensors::select('sensors.id','sensors.name', 'sensors.equipment_type_id','locations.id as location_id', 'equipment_types.type_name', 'locations.name as location_name')
        ->join('equipment_types', 'sensors.equipment_type_id', '=', 'equipment_types.id')
        ->join('sensor_locations', 'sensors.id', '=', 'sensor_locations.sensor_id')
        ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
        ->where('sensors.id', '=', "$selectsensors")
        ->Where('locations.name', 'like', "%{$selectProvince}%")
        ->get();
        }else{
            $query = Sensors::select('sensors.id','sensors.name', 'sensors.equipment_type_id','locations.id as location_id', 'equipment_types.type_name', 'locations.name as location_name')
            ->join('equipment_types', 'sensors.equipment_type_id', '=', 'equipment_types.id')
            ->join('sensor_locations', 'sensors.id', '=', 'sensor_locations.sensor_id')
            ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
            ->orWhere('locations.name', 'like', "%{$selectProvince}%")
            ->get();
        }
        return DataTables::of($query)->make(true);
    }
    public function saveReport(Request $request): \Illuminate\Http\JsonResponse
    {
        $requestData = [
            'name' => $request->reportName,
            'description' => $request->description,
            'status'=> "ACTIVE",
            'start_date' => $request->dateRange,
            'end_date' => $request->dateRange
        ];
        $ReportTemplate = ReportTemplate::create([
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'status' => $requestData['status'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'user_id' => Session::get("CURRENT_USER_ID")
        ]);

        if (!empty($ReportTemplate)) {
            $parameter = $para = implode(',', $request->sldParameters);
            $data = array();
            $data = $request->selectedData;
            foreach ($data as $selectedData) {
                $report = new Reports();
                $report->report_template_id = $ReportTemplate->id;
                $report->sensor_location_id  = $selectedData['location_id'];
                $report->sensor_data_id = $selectedData['id'];
                $report->equipment_type_id = $selectedData['equipment_type_id'];
                $report->parameters = $parameter;
                $report->status = 'ACTIVE';
               $report->save();
            }
        }



        return response()->json(['message' => 'Report template saved successfully'], 200);
    }

    public function saveTemplate(Request $request): \Illuminate\Http\JsonResponse
    {
        $requestData = [
            'user_id' => Session::get("CURRENT_USER_ID"), // need to add logged user_id
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ];
        Utility::log($requestData, get_class());

        $this->reportTemplateService->saveTemplate($requestData);
        return response()->json(['message' => 'Template created successfully'], 200);

    }

    /**
     * @throws BadRequestException
     */
    public function saveTemplateWithReport(Request $request): \Illuminate\Http\JsonResponse
    {
        $requestData = [
            'user_id' => Session::get("CURRENT_USER_ID"), // need to add logged user_id
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reportData' => $request->reportData ?? []
        ];
        Utility::log($requestData, get_class());
        $this->reportTemplateService->saveTemplateWithReport($requestData);
        return response()->json(['message' => 'Report template saved successfully'], 200);

    }

    public function allTemplates(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = Session::get('CURRENT_USER_ID');
        $requestData = [
            'templateId' => $request->input('id', null),
            'searchParam' => $request->input('searchParam', null),
            'userId' => $userId
        ];
        Utility::log($requestData, __METHOD__);

        $result = $this->reportTemplateService->findTemplates($requestData);

        return DataTables::of($result)->make(true);
    }

    public function templateDetails($templateId): \Illuminate\Http\JsonResponse
    {
        Utility::log([
            "templateId" => $templateId,
            "userId" => Session::get("CURRENT_USER_ID")
        ], get_class());
        $result = $this->reportTemplateService->templateDetails($templateId);
        return response()->json($result);
    }

    public function deleteTemplate($templateId): \Illuminate\Http\JsonResponse
    {
        Utility::log([
            "userId" => Session::get("CURRENT_USER_ID"),
            "templateId" => $templateId,
        ], get_class());
        $result = $this->reportTemplateService->deleteTemplate($templateId,Session::get("CURRENT_USER"));
        return response()->json($result);
    }

    public function statusChange($templateId, $active): \Illuminate\Http\JsonResponse
    {
        Utility::log([
            "templateId" => $templateId,
            "userId" => Session::get("CURRENT_USER_ID"),
            "active" => $active,
        ], get_class());
        $result = $this->reportTemplateService->statusChange($templateId, $active);
        return response()->json($result);
    }

    public function getHistories(Request $request)
    {
        $histories = $this->templateRepo->getAllHistories($request);

        return DataTables::of($histories['data'])
            ->with([
                'totalRecords' => $histories['totalRecords'],
                'numberOfPages' => $histories['numberOfPages'],
            ])
            ->make(true);
    }

    public function getPreviewData($reportId)
    {
        // Fetching distinct report data
        $reports = Reports::select('sensors.name as s_name', 'equipment_types.type_name as e_name')
            ->join('equipment_types', 'reports.equipment_type_id', '=', 'equipment_types.id')
            ->join('sensors', 'reports.sensor_data_id', '=', 'sensors.id')
            ->where('report_template_id', $reportId)
            ->distinct()
            ->get();
    
        // Fetching sensor data with associated joins
        $query = \DB::table('sensor_datas')
            ->join('reports', 'sensor_datas.sensor_location_id', '=', 'reports.sensor_location_id')
            ->join('sensor_locations', 'reports.sensor_location_id', '=', 'sensor_locations.id')
            ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
            ->join('sensors', 'reports.sensor_data_id', '=', 'sensors.id')
            ->join('equipment_types', 'reports.equipment_type_id', '=', 'equipment_types.id')
            ->where('reports.report_template_id', $reportId);
    
        // Select relevant fields
        $data = $query->select(
                'sensor_datas.id as id',
                'sensor_datas.created_at as date_time',
                'sensor_locations.location_id as location_id',
                'locations.name as location',
                'sensors.name as sensor_name',
                'equipment_types.type_name',
                'sensor_datas.pm2_5',
                'sensor_datas.pm10',
                'sensor_datas.o3',
                'sensor_datas.co',
                'sensor_datas.no2',
                'sensor_datas.so2',
                'sensor_datas.co2',
                'sensor_datas.aqi',
                'sensor_datas.status',
                'sensors.equipment_type_id'
            )
            ->orderBy('sensor_datas.created_at', 'desc')
            ->get();
    
        // Merging both datasets into a single array
        $response = [
            'reports' => $reports,
            'data' => $data
        ];
    
        return response()->json($response);
    }
    
}
