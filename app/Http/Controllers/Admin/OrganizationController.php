<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\ModelsV2\Organization;
use App\ModelsV2\UserOrganization;
use Illuminate\Http\Request;


class OrganizationController extends Controller{
    
    public function getOrganizations(){
    
        $organizations = Organization::get();
        return response()->json($organizations);
    }
    

}