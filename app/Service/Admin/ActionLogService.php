<?php

namespace App\Service\Admin;

use App\Repository\Admin\AdminActionLogRepo;

class ActionLogService
{

    private AdminActionLogRepo $actionLogRepo;

    public function __construct(AdminActionLogRepo $actionLogRepo)
    {
        $this->actionLogRepo = $actionLogRepo;
    }

    public function allActionLogs()
    {
        return $this->actionLogRepo->allActionLogs();
    }
}
