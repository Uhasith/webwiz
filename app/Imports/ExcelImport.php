<?php

namespace App\Imports;

use App\Exceptions\BadRequestException;
use App\Helpers\Utility;
use App\Service\StorageService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;


class ExcelImport implements ToCollection
{
    protected ?array $data;

    public function collection(Collection $collection)
    {
        // TODO: Implement collection() method.
    }


    /**
     * @param $file
     * @param string $prefix
     * @param string $fileName
     * @return JsonResponse|Collection
     * @throws \Exception
     */
    public function readWinAqmsHistoricalData($file, string $prefix = '', string $fileName = ''): \Illuminate\Http\JsonResponse|Collection
    {
        $excelRows = [];
        $headers = [];
        $path = env("HISTORY_DATA_PATH") . "/" . $prefix . "/" . $fileName;
        StorageService::storeLocally($file, $path);
        StorageService::storeCloud($path);
        if (Storage::exists($path)) {
            $excelData = Excel::toArray([], $path);
            foreach ($excelData[0] as $mainIndex => $data) {
                if (sizeof($data) < Utility::$ecoTechExcelColumnCount) {
                    throw new BadRequestException("Excel columns mismatched", 400);
                }
                if ($mainIndex == 0) {
                    $headers = $data;
                } else {
                    Utility::validateNumericArray([$data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12], $data[13], $data[14], $data[15], $data[16], $data[17], $data[18]]);
                    $date = Carbon::parse(Utility::excelDateToCarbon($data[0]));
                    if ($date->second === 59) {
                        $date->addSecond();
                    }

                    $excelRows[] = [
                        'created_at' => Utility::convertLocalToUtc($date, Utility::$sriLankaTimeZone),
                        'updated_at' => Carbon::now(),
                        $headers[2] => round($data[2] ?? 0, 2),
                        $headers[3] => round($data[3] ?? 0, 2),
                        $headers[4] => round($data[4] ?? 0, 2),
                        $headers[5] => round($data[5] ?? 0, 2),
                        $headers[6] => round($data[6] ?? 0, 2),
                        $headers[7] => round(($data[7] ?? 0) * 3.6, 2), // ms -> kmh
                        $headers[8] => round($data[8] ?? 0, 2),
                        $headers[9] => round(($data[9] ?? 0) * 3.6, 2), // ms -> kmh
                        $headers[10] => round($data[10] ?? 0, 2),
                        $headers[11] => round($data[11] ?? 0, 2),
                        $headers[12] => round($data[12] ?? 0, 2),
                        $headers[13] => round($data[13] ?? 0, 2),
                        $headers[14] => round($data[14] ?? 0, 2),
                        $headers[15] => round($data[15] ?? 0, 2),
                        $headers[16] => round($data[16] ?? 0, 2),
                        $headers[17] => round($data[17] ?? 0, 2),
                        $headers[18] => round($data[18] ?? 0, 2),
                    ];
                }
            }
            StorageService::removeLocalFile($path);
            return new Collection([
                "data" => $excelRows,
                "headers" => $headers,
                "latestFileName" => $fileName
            ]);
        } else {
            return response()->json(['error' => 'File not found', 'path' => $path], 404);
        }
    }

    /**
     * @param $file
     * @param string $prefix
     * @param string $fileName
     */
    public function nbroHistoricalData($file, string $prefix = '', string $fileName = ''): Collection
    {
        $excelRows = [];
        $headers = [];
        return new Collection([
            "data" => $excelRows,
            "headers" => $headers,
            "latestFileName" => $fileName
        ]);
    }

    /**
     * @param $file
     * @param string $prefix
     * @param string $fileName
     */
    public function tsiHistoricalData($file, string $prefix = '', string $fileName = ''): Collection
    {
        $excelRows = [];
        $headers = [];
        return new Collection([
            "data" => $excelRows,
            "headers" => $headers,
            "latestFileName" => $fileName
        ]);
    }

    /**
     * @param $file
     * @param string $prefix
     * @param string $fileName
     * @return JsonResponse|Collection
     */
    public function iqAirHistoricalData($file, string $prefix = '', string $fileName = ''): JsonResponse|Collection
    {
        $excelRows = [];
        $headers = [];
        $path = env("HISTORY_DATA_PATH") . "/" . $prefix . "/" . $fileName;
        StorageService::storeLocally($file, $path);
        StorageService::storeCloud($path);
        if (Storage::exists($path)) {
            $excelData = Excel::toArray([], $path);
            foreach ($excelData[0] as $mainIndex => $data) {
                if (sizeof($data) < Utility::$iqAirExcelColumnCount) {
                    throw new BadRequestException("Excel columns mismatched", 400);
                }
                if ($mainIndex == 0) {
                    $headers = $data;
                } else {
                    Utility::validateNumericArray([$data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12], $data[13], $data[14], $data[16], $data[20], $data[21], $data[22]]);
                    if (strtotime($data[2]) === false) {
                        throw new InvalidArgumentException("Invalid period start time found in array: $data[2]");
                    }
                    $excelRows[] = [
                        'created_at' => Utility::convertLocalToUtc($data[2], Utility::$sriLankaTimeZone),
                        'updated_at' => Carbon::now(),
                        "PM2.5 Conc" => round($data[6] ?? 0, 2),
                        "PM10 Conc" => round($data[7] ?? 0, 2),
                        "PM1" => round($data[8] ?? 0, 2),
                        "CO2" => round($data[9] ?? 0, 2), // ppm
                        "CO Conc" => round($data[10] ?? 0, 2) * 1000, // ppm -> ppb
                        "VOC" => round($data[11] ?? 0, 2),
                        "NOx Conc" => round($data[12] ?? 0, 2),
                        "O3 Conc" => round($data[13] ?? 0, 2),
                        "Temperature" => round($data[14] ?? 0, 2),
                        "Humidity" => round($data[16] ?? 0, 2),
                        "Pressure" => round($data[20] ?? 0, 2),
                        "Particle Count" => round($data[21] ?? 0, 2),
                        "Fan Speed" => round($data[22] ?? 0, 2)
                    ];
                }
            }
            StorageService::removeLocalFile($path);
            return new Collection([
                "data" => $excelRows,
                "headers" => $headers,
                "latestFileName" => $fileName
            ]);
        } else {
            return response()->json(['error' => 'File not found', 'path' => $path], 404);
        }
    }

}

