<?php

namespace App\Http\Controllers\User;

use App\Helpers\Loggers;
use App\Http\Controllers\Controller;
use App\Jobs\SensorsJob;
use App\Service\User\LocationsService;
use App\Service\User\SensorDataService;
use App\Service\User\SensorsService;
use App\Service\User\WeatherService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\ModelsV2\Sensors;
use App\ModelsV2\SensorDatas;
use App\ModelsV2\EquipmentType;
use App\Models\User;
use App\Helpers\Utility;
use App\Jobs\SendNotificationJob;
use App\Service\User\AverageCalculation\AverageMockService;
use App\Service\User\EmailService;
use App\Service\User\FirebaseService;
use App\Service\User\NotificationsService;

class DashboardController extends Controller
{
    private $sensorsService;
    private $locationsService;
    private $sensorDataService;
    private $weatherService;
    private $notificationService;
    private $firebaseService;



    public function __construct(SensorsService  $sensorsService, LocationsService $locationsService, SensorDataService $sensorDataService, WeatherService $weatherService, NotificationsService $notificationService, FirebaseService $firebaseService)
    {
        $this->sensorsService =  $sensorsService;
        $this->locationsService = $locationsService;
        $this->sensorDataService = $sensorDataService;
        $this->weatherService = $weatherService;
        $this->notificationService =  $notificationService;
        $this->firebaseService =  $firebaseService;

    }

    public function index()
    {
        Utility::log([],get_class());
        $equipmentTypes = $this->sensorsService->homepageService();
        $sensors = $this->sensorsService->getSensorsByEquipmentId('all');

        $locations = $this->locationsService->getCitiesWithProvince('');

        return Inertia::render('Userview/Home', [
            'initialLocations' => $locations,
            'equipmentTypes' => $equipmentTypes,
            'sensors' => $sensors,
            'pollutantParameters' => config('pollutantparameters')
        ]);
    }

    public function getSensorsByEquipmentId($equipmentTypeId)
    {
        Utility::log(['equipment_type_id' => $equipmentTypeId],get_class());
        return $this->sensorsService->getSensorsByEquipmentId($equipmentTypeId);
    }

    public function getDashboardData()
    {
        Utility::log([],get_class());
        $totalSensors = Sensors::count();
        $totalEquipmentTypes = EquipmentType::count();
        $activeSensors = Sensors::where('status', Utility::$statusActive)->count();
        $inactiveSensors = Sensors::where('status', Utility::$statusInactive)->count();
        $activeUsers = User::where('status', Utility::$statusActive)->count();
        $newUsersThisMonth = User::whereYear('created_at', date('Y'))
                              ->whereMonth('created_at', date('m'))
                              ->count();
        return response()->json([
            "totalSensors" => $totalSensors,
            'totalEquipmentTypes' => $totalEquipmentTypes,
            'activeSensors' => $activeSensors,
            'inactiveSensors' => $inactiveSensors,
            'activeUsers' => $activeUsers,
            'newUsersThisMonth' => $newUsersThisMonth,
        ]);
    }


    public function getRecentData(Request $request)
    {

        $requestData = [
            'sensorIds' => $request['sensorIds'] ?? [],
            'equipmentIds' => $request['equipmentIds'] ?? [],
            'sensorLocationId' => $request['sensorLocationId'] ?? null,
            'ranking' => $request['ranking'] ?? null,
        ];
        Utility::log($requestData,get_class());


        $data = $this->sensorDataService->getRecentData($requestData['sensorIds'], $requestData['equipmentIds'], $requestData['sensorLocationId'],$requestData['ranking']);
        $defaultData = $this->sensorDataService->getDefaultLocationRecentData($requestData['sensorIds'], $requestData['equipmentIds'], $requestData['sensorLocationId']);


        return response()->json([
            "data" => $data,
            "defaultData" => $defaultData
        ]);
    }
    public function getRecent24HData(Request $request)
    {

        $requestData = [
            'sensor_location_id' => $request['sensor_location_id'] ?? null
        ];
        Utility::log($requestData,get_class());

        $trendData = $this->sensorsService->get24HTrendData($requestData['sensor_location_id']);
        return response()->json($trendData);
    }


    public function airQualityData(Request $request)
    {
        $data = [
            'sensor_location_id' => $request->sensor_location_id,
            'PM2.5' => $request->pm2_5,
            'PM10' => $request->pm10,
            'O3' => $request->o3,
            'CO' => $request->co,
            'NO2' => $request->no2,
            'SO2' => $request->so2,
            'CO2' => $request->co2,
            'temperature'=>$request->temperature,
            'humidity'=> $request->humidity,
        ];

        Utility::log($data,get_class());

        $sensorData = $this->sensorDataService->saveSensorData($data);
        return response()->json($sensorData);
    }

    public function getSensorsByLocationId($locationId)
    {
        Utility::log(['location_id' => $locationId],get_class());
        $sensors = $this->sensorsService->getSensorsByLocationId($locationId);
        return response()->json($sensors);
    }


    public function getAllSensorLocations(Request $request)
    {
        Utility::log([],get_class());
        $locations = $this->locationsService->getAllSensorLocations();
        return response()->json($locations);
    }

    public function getSensorData(Request $request)
    {
        $requestData = [
            'locationId' => $request->locationId,
            'equipmentId' => $request->equipmentId,
            'timeStart' => $request->startTime ? Carbon::parse(Utility::convertLocalToUtc($request->startTime.' 00:00:00',Utility::$sriLankaTimeZone)) : null,
            'timeEnd' => $request->endTime ? Carbon::parse(Utility::convertLocalToUtc($request->endTime.' 00:00:00',Utility::$sriLankaTimeZone)) : null,
        ];

        Utility::log($requestData,get_class());

        $data = $this->sensorDataService->getSensorData($requestData['locationId'], $requestData['equipmentId'], $requestData['timeStart'], $requestData['timeEnd']);
        return response()->json($data);
    }


    //temporary save weather records api
    public function saveTempWeather($sensorLocationId, Request $request)
    {

        $data = [
            'sensorLocationId' => $sensorLocationId,
            'sensorDataId'=>$sensorDataId,
            'temperature' => $request->temperature,
            'pressure' => $request->pressure,
            'humidity' => $request->humidity,
            'cloud' => $request->cloud,
            'wind' => $request->wind,
        ];

        Utility::log($data,get_class());
        $weather = $this->weatherService->saveTempWeather($data,$data['sensorDataId']);
        return response()->json($weather);
    }

    public function citiesWithProvince(Request $request)
    {
        $data = [
            'searchTerm' => $request->input('q'),
            'limit' => $request->input('limit'),
        ];

        Utility::log($data,get_class());
        $locations = $this->locationsService->getCitiesWithProvince($data['searchTerm'], $data['limit']);

        return response()->json($locations);
    }

    //temporary for IQ air api testing
    public function iqAirSave(Request $request){

        Utility::log(["Testing Api"],get_class());
        SensorsJob::dispatch();
        return response()->json("saved");

    }

    //temporary for email testing
    public function sendEmail(Request $request){

        $data = [
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' =>str()->random(6),
        ];

      return EmailService::registerEmail($data);
    }


    public function saveNotification(Request $request){

        $data = [
            'user_id' =>  $request->input('user_id') ,
            'receiver_id' =>  $request->input('receiver_id'),
            'type' =>  $request->input('type'),
            'title' =>  $request->input('title') ,
            'description' =>  $request->input('description'),
        ];

        Utility::log($data,get_class());
        return $this->notificationService->save($data);

    }

    public function getNotifications(){

        Utility::log([],get_class());
        return $this->notificationService->getAll();
    }

    public function saveFirebaseToken(Request $request)
    {
        Utility::log([],get_class());
        return $this->firebaseService->saveToken($request->token);
    }


    public function sendNotification(Request $request)
    {
        $data = [
            'receiver_id' =>  $request->input('receiver_id'),
            'title' =>  $request->input('title') ,
            'description' =>  $request->input('description'),
        ];
        return $this->firebaseService->sendFcmNotification($data);
    }


    public function sendNotificationJobTest(){
        SendNotificationJob::dispatch();
    }
}
