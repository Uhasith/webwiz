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
                    $excelRows[] = [
                        'created_at' => Utility::convertLocalTimeStampToUtc((int)round($data[1]), Utility::$sriLankaTimeZone),
                        'updated_at' => Carbon::now(),
                        $headers[2] => round($data[2] ?? 0, 1),
                        $headers[3] => round($data[3] ?? 0, 1),
                        $headers[4] => round($data[4] ?? 0, 1),
                        $headers[5] => round($data[5] ?? 0, 1),
                        $headers[6] => round($data[6] ?? 0, 1),
                        $headers[7] => round($data[7] ?? 0, 1),
                        $headers[8] => round($data[8] ?? 0, 1),
                        $headers[9] => round($data[9] ?? 0, 1),
                        $headers[10] => round($data[10] ?? 0, 1),
                        $headers[11] => round($data[11] ?? 0, 1),
                        $headers[12] => round($data[12] ?? 0, 1),
                        $headers[13] => round($data[13] ?? 0, 1),
                        $headers[14] => round($data[14] ?? 0, 1),
                        $headers[15] => round($data[15] ?? 0, 1),
                        $headers[16] => round($data[16] ?? 0, 1),
                        $headers[17] => round($data[17] ?? 0, 1),
                        $headers[18] => round($data[18] ?? 0, 1),
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
            return response()->json(['error' => 'File not found'], 404);
        }
    }

    /**
     * @param $file
     * @param $prefix
     * @param $fileName
     */
    public function nbroHistoricalData($file, $prefix = '', $fileName = '')
    {

    }

    /**
     * @param $file
     * @param $prefix
     * @param $fileName
     */
    public function tsiHistoricalData($file, $prefix = '', $fileName = '')
    {

    }

    /**
     * @param $file
     * @param $prefix
     * @param $fileName
     * @return void
     */
    public function iqAirHistoricalData($file, $prefix = '', $fileName = '')
    {

    }

}

