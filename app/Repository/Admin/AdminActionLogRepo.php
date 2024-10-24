<?php

namespace App\Repository\Admin;

use App\Helpers\Utility;
use App\ModelsV2\UserLogs;

class AdminActionLogRepo
{

    public function saveActionLog($userId,$type,$description): UserLogs
    {
        $actionLog = new UserLogs();
        $actionLog->user_id = $userId;
        $actionLog->type = $type;
        $actionLog->description = $description;
        $actionLog->save();
        return $actionLog->refresh();
    }

    public function allActionLogs()
    {
        return UserLogs::where('status', Utility::$statusActive)->get();
    }

}
