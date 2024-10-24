<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ModelsV2\Sensors;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Service\Admin\EquipmentManagementService;
use App\Service\User\LocationsService;
use App\Service\User\SensorsService;
use App\Repository\Admin\SensorRepo;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Exports\EquipmentExport;
use App\Exports\EquipmentRecordExport;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentController extends Controller
{
    protected $equipmentManagementService;
    private $sensorsService;
    private $locationsService;
    protected $sensorsRepo;

    public function __construct(
        EquipmentManagementService $equipmentManagementService,
        SensorsService  $sensorsService,
        LocationsService $locationsService,
        SensorRepo $sensorsRepo
    ) {
        $this->equipmentManagementService = $equipmentManagementService;
        $this->sensorsService =  $sensorsService;
        $this->locationsService = $locationsService;
        $this->sensorsRepo = $sensorsRepo;
    }
    public function index(Request $request)
    {

        // return Sensors::with('equipmentType')->paginate($perPage);
        $equipments = $this->equipmentManagementService->getPaginatedUsersWithRoles(10);

        if ($request->wantsJson()) {
            return $equipments;
        }

        return Inertia::render('Adminview/EquipmentManagement', [
            'initialEquipments' => $equipments
        ]);
    }

    public function index2(Request $request)
    {
        $equipments = Sensors::with('equipmentType')->paginate(10);

        $equipmentTypes = $this->sensorsService->homepageService();
        $sensors = $this->sensorsService->getSensorsByEquipmentId('all');

        return Inertia::render('Adminview/EquipmentManagement2', [
            'initialEquipments' => $equipments,
            'equipmentTypes' => $equipmentTypes,
            'sensors' => $sensors
        ]);
    }

    public function getEquipmentsData(Request $request)
    {
        $searchTerm = $request->get('search');
        //$query = Sensors::with('equipmentType')->select(['id', 'name', 'status', 'equipment_type_id']);
        $query = Sensors::select('sensors.name', 'sensors.status', 'equipment_types.type_name', 'organization.name as organization_name')
        ->join('equipment_types', 'sensors.equipment_type_id', '=', 'equipment_types.id')
        ->join('sensor_organization', 'sensors.id', '=', 'sensor_organization.sensor_id')
        ->join('organization', 'sensor_organization.organization_id', '=', 'organization.id')
        ->where('sensors.name', 'like', "%{$searchTerm}%")
        ->orWhere('sensors.status', 'like', "%{$searchTerm}%")
        ->orWhere('equipment_types.type_name', 'like', "%{$searchTerm}%")
        ->orWhere('organization.name', 'like', "%{$searchTerm}%")
        ->get();


        return DataTables::of($query)
            ->addColumn('equipmentType', function ($row) {
                return $row->equipmentType ? $row->equipmentType->type_name : 'N/A';
            })
            ->make(true);
    }

    public function getRecordData(Request $request)
    {
        $searchTerm = $request->get('search');
        $query =  \DB::table('sensors')
        ->select('sensors.name as sensor_name', 'locations.name as location_name', 'sensor_locations.status as sensor_locations_status', 'sensor_locations.updated_at as updated_at')
        ->join('sensor_locations', 'sensors.id', '=', 'sensor_locations.sensor_id')
        ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
        ->orWhere('sensors.name', 'like', "%{$searchTerm}%")
        ->orWhere('locations.name', 'like', "%{$searchTerm}%")
        ->orWhere('sensor_locations.status', 'like', "%{$searchTerm}%")
        ->get();

        return DataTables::of($query)
            ->make(true);
    }

    public function getEquipments()
    {
        return $this->sensorsRepo->getEquipmentsWithCount();
    }

    public function exportEquipments(Request $request)
    {
        return Excel::download(new EquipmentExport($request), 'equipments.xlsx');
    }

    public function exportEquipmentRecords(Request $request)
    {
        return Excel::download(new EquipmentRecordExport($request), 'equipment-records.xlsx');
    }

}
