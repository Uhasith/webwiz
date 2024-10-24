<?php

namespace App\Repository\Admin;

use App\Helpers\Utility;
use App\ModelsV2\ReportTemplate;
use Illuminate\Support\Facades\DB;
use App\ModelsV2\Reports;

class TemplateRepo
{
    public function saveTemplate($data): ReportTemplate
    {
        $template = new ReportTemplate();
        $template->name = $data['name'];
        $template->description = $data['description'];
        $template->status = Utility::$statusActive;
        $template->start_date = $data['start_date'];
        $template->end_date = $data['end_date'];
        $template->user_id = $data['user_id'];
        $template->save();
        return $template->refresh();

    }

    public function findTemplates($templateId = null, $searchParam = null)
    {
        if ($templateId) {
            return ReportTemplate::where('status', Utility::$statusActive)
                ->where('id', $templateId)->first();
        }

        if ($searchParam) {
            return ReportTemplate::where('status', Utility::$statusActive)
                ->where(function ($query) use ($searchParam) {
                    $query->where('name', 'like', "%{$searchParam}%");
                    $query->orWhere('description', 'like', "%{$searchParam}%");
                })
                ->orderBy('created_at', 'desc')->get();
        }
        return ReportTemplate::where('status', Utility::$statusActive)
            ->orderBy('created_at', 'desc')->get();
    }

    public function deleteTemplate($templateId)
    {
        return ReportTemplate::where('id', $templateId)->delete();

    }

    public function statusChange($templateId, $active)
    {
        if ($active) {
            return ReportTemplate::where('id', $templateId)->update([
                "status" => Utility::$statusActive
            ]);
        }
        return ReportTemplate::where('id', $templateId)->update([
            "status" => Utility::$statusInactive
        ]);

    }

    public function templateDetails($templateId)
    {
        return ReportTemplate::where('id', $templateId)
            ->where('status', Utility::$statusActive)
            ->with('user')
            ->with('reports.sensorData')
            ->with('reports.sensorLocation')->first();

    }


    public function getAllHistories($request): array
    {
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $searchTerm = $request->get('search', '');

        $query = DB::table('report_history')
            ->join('users', 'report_history.user_id', '=', 'users.id')
            ->join('report_templates', 'report_history.template_id', '=', 'report_templates.id')
            ->where(function ($query) use ($searchTerm) {
                if (!empty($searchTerm)) {
                    $query->where('report_templates.name', 'like', "%{$searchTerm}%");
                }
            });

        $totalRecords = $query->count();
        $numberOfPages = ceil($totalRecords / $perPage);

        $data = $query
            ->select(
                'report_history.id as id',
                'users.name as username',
                'report_templates.description',
                'report_templates.name',
                'report_history.created_at'
            )
            ->orderBy('report_history.created_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        return [
            'data' => $data,
            'totalRecords' => $totalRecords,
            'numberOfPages' => $numberOfPages
        ];
    }
}
