<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;

class EquipmentRecordExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected Request $request;

    // Accept the request object in the constructor
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // Fetch the required data including sensor locations and organization
        return \DB::table('sensors')
            ->select(
                'sensors.name as equipment_name',
                'locations.name as location_name',
                'sensor_locations.updated_at as last_update',
                'sensor_locations.status as status'
            )
            ->join('sensor_locations', 'sensors.id', '=', 'sensor_locations.sensor_id')
            ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
            ->get(); // Return a collection of sensors with location, last update, and status
    }

    public function headings(): array
    {
        return [
            'Equipment Name',
            'Location',
            'Last Update',
            'Status',
        ];
    }

    public function map($sensor): array
    {
        return [
            $sensor->equipment_name,
            $sensor->location_name,
            Carbon::parse($sensor->last_update)->toDateTimeString(), // Format the last update
            ucfirst($sensor->status),
        ];
    }

    // Apply cell styles to center the placeholders
    public function styles(Worksheet $sheet)
    {
        // Apply center alignment for all columns
        $sheet->getStyle('A1:D1000')->getAlignment()->setHorizontal('center'); // Adjust range as needed
    }

    // Optional: Apply additional formatting after sheet creation
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-size columns for better readability
                foreach (range('A', 'D') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }
}

