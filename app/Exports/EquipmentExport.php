<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;

class EquipmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $sensors = \DB::table('sensors')
            ->select(
                'sensors.name as equipment_name',
                'equipment_types.type_name as equipment_type',
                'organization.name as organization_name',
                'sensors.status as status'
            )
            ->join('equipment_types', 'sensors.equipment_type_id', '=', 'equipment_types.id')
            ->join('sensor_organization', 'sensors.id', '=', 'sensor_organization.sensor_id')
            ->join('organization', 'sensor_organization.organization_id', '=', 'organization.id')
            ->get();

        $data = [];
        foreach ($sensors as $sensor) {
            $data[] = [
                'Equipment Name' => $sensor->equipment_name,
                'Equipment Type' => $sensor->equipment_type,
                'Manufacturer' => $sensor->organization_name,
                'Description' => '',
                'Status' => $sensor->status,
                'Entered By' => '',
            ];
        }

        return collect($data);
    }


    public function headings(): array
    {
        return [
            'Equipment Name',
            'Equipment Type',
            'Menufeturer',
            'Description',
            'Status',
            'Enter By',
        ];
    }

    /**
     * @param $equipment
     * @return array
     */
    public function map($equipment): array
    {
        return [
            $equipment['Equipment Name'],
            $equipment['Equipment Type'],
            $equipment['Manufacturer'],
            $equipment['Description'] ?? '-',
            $equipment['Status'],
            $equipment['Entered By'] ?? '-',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:F1000')->getAlignment()->setHorizontal('center'); // Modify range as needed
    }

    /**
     * @return mixed
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-size columns for better readability
                foreach (range('A', 'F') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }
}
