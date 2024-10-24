<?php

namespace App\Repository\Admin;

use App\Helpers\Utility;
use App\ModelsV2\Reports;

class ReportRepo
{

    public function saveReport($data)
    {
        return Reports::insert($data);
    }

    public function reportDetails($reportId = null, $templateId = null)
    {
        $query = Reports::where('status', Utility::$statusActive);
        if ($reportId) {
            $query->where('id', $reportId);
        }
        if ($templateId) {
            $query->where('report_template_id', $templateId);
        }
        $query->with(['sensorData', 'sensorLocation', 'reportTemplate']);
        return $query->first();
    }

    public function deleteReport($reportId = null, $templateId = null)
    {
        if ($reportId) {
            return Reports::where('id', $reportId)->delete();
        }
        if ($templateId) {
            return Reports::where('report_template_id', $templateId)->delete();
        }
    }
}
