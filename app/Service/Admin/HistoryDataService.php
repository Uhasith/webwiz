<?php

namespace App\Service\Admin;

use App\AqiService;
use App\Exceptions\BadRequestException;
use App\Helpers\Utility;
use App\Imports\ExcelImport;
use App\ModelsV2\AnuallySensorData;
use App\ModelsV2\AnuallyWeatherData;
use App\ModelsV2\DailySensorData;
use App\ModelsV2\DailyWeatherData;
use App\ModelsV2\HourlySensorData;
use App\ModelsV2\HourlyWeatherData;
use App\ModelsV2\MonthlySensorData;
use App\ModelsV2\MonthlyWeatherData;
use App\ModelsV2\OptionalSensorData;
use App\Repository\Admin\HistoricalDataRepo;
use App\Repository\Admin\SensorRepo;
use App\Repository\Admin\LocationsRepo;
use App\Repository\User\AnuallySensorDataRepo;
use App\Repository\User\AnuallyWeatherDataRepo;
use App\Repository\User\DailySensorDataRepo;
use App\Repository\User\DailyWeatherDataRepo;
use App\Repository\User\HourlySensorDataRepo;
use App\Repository\User\HourlyWeatherDataRepo;
use App\Repository\User\MonthlySensorDataRepo;
use App\Repository\User\MonthlyWeatherDataRepo;
use App\Repository\User\OptionalSensorDataRepo;
use App\Repository\User\SensorLocationRepo;
use App\Service\User\AverageCalculation\AverageFactory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HistoryDataService
{
    private AverageFactory $averageFactory;
    private SensorRepo $sensorRepo;
    private LocationsRepo $locationsRepo;
    private SensorLocationRepo $sensorLocationRepo;
    private HourlySensorDataRepo $hourlySensorDataRepo;
    private AqiService $aqiService;
    private HourlyWeatherDataRepo $hourlyWeatherDataRepo;
    private DailySensorDataRepo $dailySensorDataRepo;
    private DailyWeatherDataRepo $dailyWeatherDataRepo;
    private MonthlySensorDataRepo $monthlySensorDataRepo;
    private MonthlyWeatherDataRepo $monthlyWeatherDataRepo;
    private AnuallySensorDataRepo $anuallySensorDataRepo;
    private AnuallyWeatherDataRepo $anuallyWeatherDataRepo;
    private OptionalSensorDataRepo $optionalDataRepo;
    private HistoricalDataRepo $historicalDataRepo;

    /**
     * @param SensorRepo $sensorRepo
     * @param LocationsRepo $locationsRepo
     * @param SensorLocationRepo $sensorLocationRepo
     * @param HourlySensorDataRepo $hourlySensorDataRepo
     * @param AqiService $aqiService
     * @param OptionalSensorDataRepo $optionalDataRepo
     * @param HourlyWeatherDataRepo $hourlyWeatherDataRepo
     * @param DailySensorDataRepo $dailySensorDataRepo
     * @param DailyWeatherDataRepo $dailyWeatherDataRepo
     * @param MonthlySensorDataRepo $monthlySensorDataRepo
     * @param MonthlyWeatherDataRepo $monthlyWeatherDataRepo
     * @param AnuallySensorDataRepo $anuallySensorDataRepo
     * @param AnuallyWeatherDataRepo $anuallyWeatherDataRepo
     * @param HistoricalDataRepo $historicalDataRepo
     */
    public function __construct(SensorRepo            $sensorRepo, LocationsRepo $locationsRepo, SensorLocationRepo $sensorLocationRepo,
                                HourlySensorDataRepo  $hourlySensorDataRepo, AqiService $aqiService, OptionalSensorDataRepo $optionalDataRepo,
                                HourlyWeatherDataRepo $hourlyWeatherDataRepo, DailySensorDataRepo $dailySensorDataRepo, DailyWeatherDataRepo $dailyWeatherDataRepo,
                                MonthlySensorDataRepo $monthlySensorDataRepo, MonthlyWeatherDataRepo $monthlyWeatherDataRepo, AnuallySensorDataRepo $anuallySensorDataRepo, AnuallyWeatherDataRepo $anuallyWeatherDataRepo,HistoricalDataRepo $historicalDataRepo
    )
    {
        $this->averageFactory = new AverageFactory();
        $this->sensorRepo = $sensorRepo;
        $this->locationsRepo = $locationsRepo;
        $this->sensorLocationRepo = $sensorLocationRepo;
        $this->hourlySensorDataRepo = $hourlySensorDataRepo;
        $this->aqiService = $aqiService;
        $this->hourlyWeatherDataRepo = $hourlyWeatherDataRepo;
        $this->dailySensorDataRepo = $dailySensorDataRepo;
        $this->dailyWeatherDataRepo = $dailyWeatherDataRepo;
        $this->monthlySensorDataRepo = $monthlySensorDataRepo;
        $this->monthlyWeatherDataRepo = $monthlyWeatherDataRepo;
        $this->anuallySensorDataRepo = $anuallySensorDataRepo;
        $this->anuallyWeatherDataRepo = $anuallyWeatherDataRepo;
        $this->optionalDataRepo = $optionalDataRepo;
        $this->historicalDataRepo = $historicalDataRepo;
    }

    /**
     * @param $data
     * @return JsonResponse|ExcelImport|Collection
     * @throws BadRequestException
     */
    public function uploadHistoryData($data): \Illuminate\Http\JsonResponse|ExcelImport|\Illuminate\Support\Collection
    {
//        set_time_limit(120);

        $sensor = $this->sensorRepo->findSensorById($data['equipmentId']);
        $fileName = ($data['file'])->getClientOriginalName();

        if(!$sensor){
            Log::error("Failed to restore historical data. Invalid sensor provided", ["filename" => $fileName, "equipmentId" => $data['equipmentId'], "locationId" => $data['locationId']]);
            throw new BadRequestException("Failed to restore historical data. Invalid sensor provided", 400);
        }
        $prefix = $sensor->slug;
        $sensorLocationId = null;

        if ($data['locationId'] != 'all') {
            $location = $this->locationsRepo->findById($data['locationId']);
            if (!$location) {
                Log::error("Failed to restore historical data. Invalid location | sensor provided", ["filename" => $fileName, "equipment" => $sensor->slug, "equipmentId" => $data['equipmentId'], "locationId" => $data['locationId']]);
                throw new BadRequestException("Failed to restore historical data. Invalid location | sensor provided", 400);
            }
            $sensorLocation = $this->sensorLocationRepo->getSensorLocationBySensorAndLocation($sensor->id, $location->id);
            if (!$sensorLocation) {
                Log::error("Failed to restore historical data. Invalid location | sensor provided", ["filename" => $fileName, "equipment" => $sensor->slug, "equipmentId" => $data['equipmentId'], "locationId" => $data['locationId']]);
                throw new BadRequestException("Failed to restore historical data. Invalid location | sensor provided", 400);
            }
            $sensorLocationId = $sensorLocation->id;
            $prefix = $prefix . "-" . $location->name;
        } else {
            $prefix = $prefix . "-" . 'all';
        }
        $importedData = new ExcelImport();
        DB::beginTransaction();
        try {
            if ($sensor->slug === Utility::$ecoTechSlug) {
                $importedData = $importedData->readWinAqmsHistoricalData($data['file'], $prefix, strtolower(str_replace(' ', '', $fileName)));
            } else if ($sensor->slug === Utility::$iqAirSlug) {
                $importedData = $importedData->iqAirHistoricalData($data['file'], $prefix, strtolower(str_replace(' ', '', $fileName)));
            } else if ($sensor->slug === Utility::$nbroSlug) {
                $importedData = $importedData->nbroHistoricalData($data['file'], $prefix, strtolower(str_replace(' ', '', $fileName)));
            } else if ($sensor->slug === Utility::$tsiBlueskySlug) {
                $importedData = $importedData->tsiHistoricalData($data['file'], $prefix, strtolower(str_replace(' ', '', $fileName)));
            }
            $this->store($importedData, $sensor, $sensorLocationId, $fileName);
            DB::commit();
            Log::info("Historical data successfully restored.", ["filename" => $fileName, "equipment" => $sensor->slug, "length" => sizeof($importedData['data'])]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BadRequestException($e->getMessage(), 400);
        }
        return response()->json(
            [
                'message' => 'Historical data successfully restored',
                'length' => sizeof($importedData['data']),
                'fileName' => $fileName,
                'equipment' =>$sensor->slug
            ]);
    }

    /**
     * @param $importedData
     * @param $sensor
     * @param $sensorLocationId
     * @param $fileName
     * @return void
     * @throws \Exception
     */
    private function store($importedData, $sensor, $sensorLocationId, $fileName): void
    {
        $formattedDataList = $this->formatHistoryData($importedData, $sensor->slug, $sensorLocationId, $fileName);
        if(sizeof($formattedDataList) == 0){
            throw new BadRequestException("Something went wrong uploading historical data", 400);
        }
        foreach ($formattedDataList as $formattedData) {
            $sensorDataEcoTech = $this->aqiService->processAirQualityData($formattedData['sensor_data'], Utility::$history, Utility::$typeManual);
            $weatherDataEcoTech = $formattedData['weather_data'];
            $optionalDataEcoTech = $formattedData['optional_data'];

            $sensorDataEcoTechArray[] = $sensorDataEcoTech;
            $weatherEcoTechDataArray[] = HourlyWeatherData::formatHourlyWeatherData($weatherDataEcoTech, $sensorDataEcoTech['id']);
            $optionalEcoTechDataArray[] = Utility::formatOptionalData($optionalDataEcoTech, $sensorDataEcoTech['id'], 'hourly');
        }

        $this->removeDuplicateIdentifiedData($fileName, $sensorLocationId);
        /** @var HourlySensorData $sensorDataEcoTechArray */
        /** @var HourlyWeatherData $weatherEcoTechDataArray */
        /** @var OptionalSensorData $optionalEcoTechDataArray */
        $this->hourlySensorDataRepo->insert((array)$sensorDataEcoTechArray);
        $this->hourlyWeatherDataRepo->insert((array)$weatherEcoTechDataArray);
        $this->optionalDataRepo->insert((array)$optionalEcoTechDataArray);

        $sensorDataCollection = collect($sensorDataEcoTechArray);
        $weatherDataCollection = collect($weatherEcoTechDataArray);

        /** Calculation of daily data by daily excel data */
        $groupedSensorData = $sensorDataCollection->groupBy(function ($item) {
            return Carbon::parse($item['created_at'])->format('Y-m-d');
        });
        $groupedWeatherData = $weatherDataCollection->groupBy(function ($item) {
            return Carbon::parse($item['created_at'])->format('Y-m-d');
        });
        $groupedSensorData->map(function ($groupedData, $outerIndex) use ($groupedWeatherData, $groupedSensorData) {
            $groupedData = $groupedData->map(function ($item, $innerIndex) use ($groupedWeatherData, $outerIndex, $groupedSensorData) {
                $item['weatherRecords'] = (object)$groupedWeatherData[$outerIndex][$innerIndex];
                return (object)$item;
            });
            $this->setDailyAvg($groupedData, Carbon::parse($outerIndex)->endOfDay()->format('Y-m-d H:i:s'));
        });

        /** Calculation of monthly data by daily excel data */
        $groupedSensorData = $sensorDataCollection->groupBy(function ($item) {
            return Carbon::parse($item['created_at'])->format('Y-m');
        });
        $groupedWeatherData = $weatherDataCollection->groupBy(function ($item) {
            return Carbon::parse($item['created_at'])->format('Y-m');
        });
        $groupedSensorData->map(function ($groupedData, $outerIndex) use ($groupedWeatherData, $groupedSensorData) {
            $groupedData = $groupedData->map(function ($item, $innerIndex) use ($groupedWeatherData, $outerIndex, $groupedSensorData) {
                $item['weatherRecords'] = (object)$groupedWeatherData[$outerIndex][$innerIndex];
                return (object)$item;
            });
            $this->setMonthlyAvg($groupedData, Carbon::parse($outerIndex)->endOfMonth()->format('Y-m-d H:i:s'));
        });

        /** Calculation of annually data by daily excel data */
        $groupedSensorData = $sensorDataCollection->groupBy(function ($item) {
            return Carbon::parse($item['created_at'])->format('Y-m');
        });
        $groupedWeatherData = $weatherDataCollection->groupBy(function ($item) {
            return Carbon::parse($item['created_at'])->format('Y-m');
        });
        $groupedSensorData->map(function ($groupedData, $outerIndex) use ($groupedWeatherData, $groupedSensorData) {
            $groupedData = $groupedData->map(function ($item, $innerIndex) use ($groupedWeatherData, $outerIndex, $groupedSensorData) {
                $item['weatherRecords'] = (object)$groupedWeatherData[$outerIndex][$innerIndex];
                return (object)$item;
            });
            $this->setAnnuallyAvg($groupedData, Carbon::parse($outerIndex)->endOfYear()->format('Y-m-d H:i:s'));
        });
    }

    /**
     * @param $data
     * @param $type
     * @param $sensorLocationId
     * @param $identifier
     * @return array
     */
    private function formatHistoryData($data, $type, $sensorLocationId, $identifier = null): array
    {
        $formattedData = [];
        foreach ($data['data'] as $historyData) {
            $formattedData [] = [
                "sensor_data" => Utility::sensorDataFormat($historyData, $sensorLocationId, $identifier),
                "weather_data" => Utility::weatherDataFormat($historyData, $sensorLocationId),
                "optional_data" => Utility::optionalDataFormat($historyData, $sensorLocationId, $identifier)
            ];
        }
        return $formattedData;
    }

    /**
     * @param $dailyData
     * @param $date
     * @return void
     * @throws \Exception
     */
    private function setDailyAvg($dailyData, $date): void
    {
        $daily = $this->averageFactory->getInstance()->daily($dailyData, $date);
        if (!empty($daily)) {
            foreach ($daily as $sensorData) {
                $dailyData = $this->aqiService->processAirQualityData($sensorData['sensor_data'], Utility::$history, Utility::$typeManual);
                $sensorDataArray[] = $dailyData;
                $weatherDataArray[] = DailyWeatherData::formatWeatherData($sensorData['weather_data'], $dailyData['id']);
            }
            /** @var DailySensorData $sensorDataArray */
            /** @var DailyWeatherData $weatherDataArray */
            $this->dailySensorDataRepo->saveDailySensorDataList((array)$sensorDataArray);
            $this->dailyWeatherDataRepo->saveDailyWeatherList((array)$weatherDataArray);
        }
    }

    /**
     * @param $monthlyData
     * @param $date
     * @return void
     * @throws \Exception
     */
    private function setMonthlyAvg($monthlyData, $date): void
    {
        $monthly = $this->averageFactory->getInstance()->monthly($monthlyData, $date);
        if (!empty($monthly)) {
            foreach ($monthly as $sensorData) {
                $monthlyData = $this->aqiService->processAirQualityData($sensorData['sensor_data'], Utility::$history, Utility::$typeManual);
                $sensorDataArray[] = $monthlyData;
                $weatherDataArray[] = MonthlyWeatherData::formatWeatherData($sensorData['weather_data'], $monthlyData['id']);
            }
            /** @var MonthlySensorData $sensorDataArray */
            /** @var MonthlyWeatherData $weatherDataArray */
            $this->monthlySensorDataRepo->saveMonthlySensorDataList((array)$sensorDataArray);
            $this->monthlyWeatherDataRepo->saveMonthlyWeatherList((array)$weatherDataArray);
        }
    }

    /**
     * @param $annuallyData
     * @param $date
     * @return void
     * @throws \Exception
     */
    private function setAnnuallyAvg($annuallyData, $date): void
    {
        $annually = $this->averageFactory->getInstance()->annually($annuallyData, $date);
        if (!empty($annually)) {
            foreach ($annually as $sensorData) {
                $annuallyData = $this->aqiService->processAirQualityData($sensorData['sensor_data'], Utility::$history, Utility::$typeManual);
                $sensorDataArray[] = $annuallyData;
                $weatherDataArray[] = AnuallyWeatherData::formatWeatherData($sensorData['weather_data'], $annuallyData['id']);
            }
            /** @var AnuallySensorData $sensorDataArray */
            /** @var AnuallyWeatherData $weatherDataArray */
            $this->anuallySensorDataRepo->saveAnuallySensorDataList((array)$sensorDataArray);
            $this->anuallyWeatherDataRepo->saveAnuallyWeatherList((array)$weatherDataArray);
        }
    }

    /**
     * @param $identifier
     * @param $sensorLocationId
     * @return void
     */
    private function removeDuplicateIdentifiedData($identifier, $sensorLocationId): void
    {
//        $this->optionalDataRepo->removeDuplicateIdentifiedData($identifier,$sensorLocationId);
        $this->hourlySensorDataRepo->removeDuplicateIdentifiedData($identifier, $sensorLocationId);
        $this->dailySensorDataRepo->removeDuplicateIdentifiedData($identifier, $sensorLocationId);
        $this->monthlySensorDataRepo->removeDuplicateIdentifiedData($identifier, $sensorLocationId);
        $this->anuallySensorDataRepo->removeDuplicateIdentifiedData($identifier, $sensorLocationId);
    }

    /**
     * @param $data
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function revertUploadedData($data): JsonResponse
    {
        $sensor = $this->sensorRepo->findSensorById($data['equipmentId']);
        $location = $this->locationsRepo->findById($data['locationId']);
        $sensorLocationId = $this->sensorLocationRepo->getSensorLocationBySensorAndLocation($sensor->id, $location->id)->id;
        $fileName = ($data['file'])->getClientOriginalName();
        if (!$sensorLocationId || !$fileName) {
            throw new BadRequestException("Failed to revert uploaded historical data", 400);
        }
        DB::beginTransaction();
        try {
            $this->removeDuplicateIdentifiedData($fileName, $sensorLocationId);
            DB::commit();
            Log::info("Historical data successfully replicated.", ["filename" => $fileName, "equipment" => $sensor->slug]);
            return response()->json(['message' => 'Uploaded historical data successfully replicated'], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return response()->json(['message' => 'Replication of uploaded historical data failed'], 400);
        }
    }

    /**
     * @param $data
     * @param $user
     * @return mixed
     */
    public function getAll($data, $user): mixed
    {
        return $this->historicalDataRepo->getAll($data,$user);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function lastUpdatedRecord($type): mixed
    {
        return $this->historicalDataRepo->lastUpdatedRecord($type);
    }

}
