<?php

namespace App\Service\Admin;

use App\Helpers\Utility;
use App\ModelsV2\ReportTemplate;
use App\Repository\Admin\AdminActionLogRepo;
use App\Repository\Admin\ReportRepo;
use App\Repository\Admin\TemplateRepo;
use Google\Cloud\Core\Exception\BadRequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webpatser\Uuid\Uuid;

class ReportTemplateService
{

    private TemplateRepo $templateRepo;
    private ReportRepo $reportRepo;
    private AdminActionLogRepo $actionLogRepo;

    /**
     * @param TemplateRepo $templateRepo
     * @param ReportRepo $reportRepo
     * @param AdminActionLogRepo $actionLogRepo
     */
    public function __construct(TemplateRepo $templateRepo, ReportRepo $reportRepo, AdminActionLogRepo $actionLogRepo)
    {
        $this->templateRepo = $templateRepo;
        $this->reportRepo = $reportRepo;
        $this->actionLogRepo = $actionLogRepo;
    }

    /**
     * @param $data
     * @return ReportTemplate
     */
    public function saveTemplate($data): \App\ModelsV2\ReportTemplate
    {
        $response = $this->templateRepo->saveTemplate($data);
        $this->actionLogRepo->saveActionLog($response->user_id, '', '');
        return $response;
    }

    /**
     * @throws BadRequestException
     */
    public function saveTemplateWithReport($data)
    {
        DB::beginTransaction();
        try {
            $templateSaved = $this->templateRepo->saveTemplate($data);
            $templateSaved = $this->saveReport($data, $templateSaved->id);
            $this->actionLogRepo->saveActionLog($templateSaved->user_id, '', '');
            DB::commit();
            return $templateSaved;
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }
        throw new BadRequestException("Failed to save report", 400);
    }

    /**
     * @throws \Exception
     */
    public function saveReport($data, $templateId)
    {
        $requestData = [];
        foreach ($data['reportData'] as $reportData) {
            $requestData [] = [
                "report_template_id" => $templateId,
                "sensor_location_id" => $reportData['sensor_location_id'],
                "sensor_data_id" => $reportData['sensor_data_id'],
                "status" => Utility::$statusActive,
            ];
        }
        return $this->reportRepo->saveReport($requestData);
    }

    /**
     * @param $requestData
     * @return mixed
     */
    public function findTemplates($requestData): mixed
    {
        return $this->templateRepo->findTemplates($requestData['templateId'], $requestData['searchParam']);
    }

    /**
     * @param $templateId
     * @return mixed
     */
    public function templateDetails($templateId): mixed
    {
        return $this->templateRepo->templateDetails($templateId);
    }

    /**
     * @param $templateId
     * @param $active
     * @return mixed
     */
    public function statusChange($templateId, $active): mixed
    {
        return $this->templateRepo->statusChange($templateId, $active);
    }

    /**
     * @param $templateId
     * @param $userId
     * @return JsonResponse
     */
    public function deleteTemplate($templateId, $userId): \Illuminate\Http\JsonResponse
    {
        $deleted = $this->templateRepo->deleteTemplate($templateId);
        $this->actionLogRepo->saveActionLog($userId, '', '');

        if ($deleted) {
            return response()->json([
                "status" => "Success",
                "message" => "Template successfully deleted"
            ]);
        }
        return response()->json([
            "status" => "Failed",
            "message" => "Failed to delete template"
        ], 400);
    }
}
