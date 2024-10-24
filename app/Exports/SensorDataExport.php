<?php

namespace App\Exports;

use App\ModelsV2\SensorDatas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;

class SensorDataExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected Request $request;

    // Accept the request object in the constructor
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return \DB::table('sensor_datas')
        ->join('sensor_locations', 'sensor_datas.sensor_location_id', '=', 'sensor_locations.id')
        ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
        ->join('sensors', 'sensor_locations.sensor_id', '=', 'sensors.id')
        ->where(function ($query) {
            $searchTerm = $this->request->get('search');
            $selectedDate = $this->request->get('selectedDate');
            $selectedStatus = $this->request->get('selectedStatus');
            $selectedEquipmentType = $this->request->get('selectedEquipmentType');
            $selectsensors = $this->request->get('selectsensors');
            $selectProvince = $this->request->get('typeFilteredProvince');

            // Apply filtering for the search term
            if (!empty($searchTerm)) {
                $query->where('locations.name', 'like', "%{$searchTerm}%");
            }

            // Apply filtering for equipment type
            if (!empty($selectedEquipmentType) && $selectedEquipmentType !== "all") {
                $query->where('sensors.equipment_type_id', '=', $selectedEquipmentType);
            }

            // Apply filtering for sensor
            if (!empty($selectsensors) && $selectsensors !== "all") {
                $query->where('sensors.id', '=', $selectsensors);
            }

            // Apply filtering for date
            if (!empty($selectedDate)) {
                $query->where('sensor_datas.created_at', 'like', "%{$selectedDate}%");
            }

            // Apply filtering for status
            if (!empty($selectedStatus)) {
                $query->where('sensor_datas.status', 'like', "%{$selectedStatus}%");
            }

            // Apply filtering for province if set
            if (!empty($selectProvince) && $selectProvince !== "All") {
                $query->where('locations.name', 'like', "%{$selectProvince}%");
            }
        })
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
            'sensors.equipment_type_id'
        )
        ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date Time',
            'Location ID',
            'Location',
            'PM2.5 (µg/m³)',
            'PM10 (µg/m³)',
            'O3 (ppb)',
            'CO (ppb)',
            'NO2 (ppb)',
            'SO2 (ppb)',
            'CO2 (ppb)',
            'AQI',
            'Status',
            'Equipment Type ID',
        ];
    }

    public function map($sensorData): array
    {
        return [
            $sensorData->id,
            $sensorData->date_time,
            $sensorData->location_id,
            $sensorData->location,
            $sensorData->pm2_5 . ' µg/m³',
            $sensorData->pm10 . ' µg/m³',
            $sensorData->o3 . ' ppb',
            $sensorData->co . ' ppb',
            $sensorData->no2 . ' ppb',
            $sensorData->so2 . ' ppb',
            $sensorData->co2 . ' ppb',
            $sensorData->aqi,
            $sensorData->status,
            $sensorData->equipment_type_id,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:O10000')->getAlignment()->setHorizontal('center');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                foreach (range('A', 'O') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }
}
