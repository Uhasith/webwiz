<?php

namespace App\Imports\Extract;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SplitTextExport implements FromCollection, WithHeadings
{
    protected ?array $data;
    protected ?string $latestFileName;

    public function __construct($prefix = 'Battaramulla')// $prefix = Ba, Ka, Mo
    {
        $this->data = WQDExtract::read($prefix);
    }

    /**
     * @return array|Collection
     */
    public function collection(): array|Collection
    {
        if (!$this->data) {
            return [];
        }

        $fileContent = $this->data['file'];
        $this->latestFileName = $this->data['latestFileName'];

        $rows = explode("\n", $fileContent);
        $headers = explode(',', trim($rows[1]));
        $dataRows = array_slice($rows, 2);
        $dataList = collect($dataRows)->map(function ($row) {
            return explode(',', $row);
        });
        $excelRows = [];
        $formattedHeaders = array_fill(0, sizeof($dataList), null);
        $postFix = explode('.', $this->latestFileName);
        foreach ($dataList as $mainIndex => $data) {
            $rowData = [];
            if (sizeof($data) > 1) {
                $formattedHeaders[$mainIndex] = array_fill(0, sizeof($data), null);
                foreach ($data as $index => $col) {
                    if($index == 0){
                        $rowData['created_at'] = intval($col);
                    }
                    $formattedHeaders[$mainIndex][$index * 2] = $headers[$index] ?? "";
                    $formattedHeaders[$mainIndex][$index * 2 + 1] = ($headers[$index] ?? "") . " status";
                    $lastValue = str_replace("\n", '', trim($data[$index+2]??''));
                    $rowData[$formattedHeaders[$mainIndex][$index]] = $lastValue;
                }
                unset($rowData['']);
                $rowData[$formattedHeaders[$mainIndex][sizeof($data)-3]] = '';
                $rowData['key'] = (str_replace("\n", '', trim($data[sizeof($data)-1])))."-".$postFix[0];
            }
            if (!empty($rowData)) {
                $excelRows[] = $rowData;
            }
        }

        return new Collection([
            "data" => $excelRows,
            "latestFileName" => $this->latestFileName
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $rows = explode("\n", $this->data);
        return explode(',', trim($rows[1]));
    }
}
