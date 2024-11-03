<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\API\SensorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\API\EquipmentTypeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\API\ExternalAccessController;
use App\Http\Controllers\Admin\DataManagementController;
use App\Http\Controllers\Admin\ReportTemplateController;
use App\Http\Controllers\Admin\UserManagementController;

if (config('app.https_enabled', false)) {
    URL::forceScheme("https");
}

Route::get('health', function () {
    return Response::make('');
});
Route::group(["prefix" => 'auth', "middleware" => ['throttle:100,1']], function () {
    Route::post('/forget-password', [UserController::class, 'forgetPassword']);
    Route::post('/set-password', [UserController::class, 'resetPassword']);
});

Route::group(["prefix" => 'admin', "middleware" => ['throttle:100,1', 'admin']], function () {
    Route::get('/logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout-now');

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])
        ->prefix('dashboard')->group(function () {
            Route::get('/', function () {
                return Inertia::render('Dashboard');
            })->name('dashboard');

            Route::get('/reports', function () {
                return Inertia::render('Adminview/ReportManagement');
            })->name('data-management');

            Route::prefix('settings')->group(function () {
                Route::get('/', function () {
                    return Inertia::render('Adminview/SettingsManagement');
                })->name('settings-management');

                Route::get('/notification-setting', function () {
                    return Inertia::render('Adminview/NotificationSetting');
                })->name('notification-setting');
            });
            Route::get('/user-management', [UserManagementController::class, 'index'])->name('usermanagement.index');
            Route::get('/data-management', [DataManagementController::class, 'index'])->name('datamanagement.index');
            Route::get('/equipments', [EquipmentController::class, 'index'])->name('equipmentmanagement.index');
            Route::get('/create-report', [ReportTemplateController::class, 'index'])->name('reporttemplate.index');
        Route::get('/external-acess', [ExternalAccessController::class, 'index'])->name('externalacess.index');

        });
    Route::get('/roles-permissions', [UserManagementController::class, 'getRolesWithPermissions']);
    Route::post('/roles/${roleId}/permission', [RoleController::class, 'syncPermissionToRole'])->name('sync.permission.to.role');
    Route::post('/roles', [RoleController::class, 'store'])->name('role.store');
    Route::delete('/roles/${roleId}', [RoleController::class, 'destroy'])->name('role.destroy');
    Route::post('/save-users', [UserController::class, 'store']);
    Route::get('/user-management/{id}/edit', [UserController::class, 'edit']);
    Route::put('/user-management/{id}', [UserController::class, 'update']);
    Route::delete('/user-management/{id}', [UserManagementController::class, 'delete']);
    Route::get('/get-roles', [RoleController::class, 'getRoles']);
    Route::get('/get-organization', [OrganizationController::class, 'getOrganizations']);
    Route::get('/user-details', [UserManagementController::class, 'getUsersData']);
    Route::get('/location/{eId}/{mId}', [DataManagementController::class, 'getLocationDetail']);
    Route::get('/equipments-data', [EquipmentController::class, 'getEquipments']);
    Route::get('/equipments-details', [EquipmentController::class, 'getEquipmentsData']);
    Route::get('/equipments-record', [EquipmentController::class, 'getRecordData']);
    Route::get('/sensor-data', [DataManagementController::class, 'getSensorData'])->middleware('cache');
    Route::put('/sensor-data', [DataManagementController::class, 'updateSensorData'])->name('sensor.data.update');
    Route::put('/sensor-data/status/update', [DataManagementController::class, 'updateSensorDataStatus'])->name('sensor.data.status.update');
    Route::get('/sensor-details', [DataManagementController::class, 'getSensorDetails']);
    Route::get('/data-management/{id}/edit', [DataManagementController::class, 'edit']);
    Route::delete('/data-management/{id}', [DataManagementController::class, 'delete']);

    Route::get('/data/equipments', [EquipmentController::class, 'getEquipmentsData'])->name('equipment.data');
    Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData']);

    Route::get('/report-templates', [ReportTemplateController::class, 'allTemplates']);
    Route::get('/report-history', [ReportTemplateController::class, 'getHistories']);
    Route::get('/report-preview/{reportId}', [ReportTemplateController::class, 'getPreviewData']);
    Route::get('/report-details', [ReportTemplateController::class, 'getEquipmentsData']);
    Route::post('/save-report', [ReportTemplateController::class, 'saveReport']);
    Route::get('export-users', [UserManagementController::class, 'exportFilteredUsers']);
    Route::get('export-sensors-data', [DataManagementController::class, 'exportFilteredSensorsData']);
    Route::get('export-equipments', [EquipmentController::class, 'exportEquipments']);
    Route::get('export-equipment-records', [EquipmentController::class, 'exportEquipmentRecords']);
    Route::post('/schedule-notification', [NotificationController::class, 'scheduleNotification']);

});

Route::group(["prefix" => 'public', "middleware" => ['throttle:100,1','general','web']], function () {
    Route::get('/latest-aqi', function () {
        return Inertia::render('Userview/Mobilemodel');
    });
    Route::get('/faq', function () {
        return Inertia::render('Userview/Faq');
    });
    Route::get('/about-us', function () {
        return Inertia::render('Userview/AboutUs');
    });
    Route::get('/comparison', [\App\Http\Controllers\User\DashboardMobileController::class, 'index']);

    Route::get('/recent-notifications', function () {
        return Inertia::render('Userview/MobileNotification');
    });
    Route::get('/equipment-types', [EquipmentTypeController::class, 'index']);
    Route::get('/sensors', [SensorController::class, 'index']);
    Route::get('/sensors/location/{locationId}', [DashboardController::class, 'getSensorsByLocationId'])->middleware('cache');
    Route::get('/sensors-data/recent', [DashboardController::class, 'getRecentData'])->middleware('cache');
    Route::get('/sensors-data/24h-recent', [DashboardController::class, 'getRecent24HData'])->middleware('cache');
    Route::get('/sensor-locations', [DashboardController::class, 'getAllSensorLocations']);
    Route::get('/sensor-data/history', [DashboardController::class, 'getSensorData']);
    Route::get('/cities-with-province', [DashboardController::class, 'citiesWithProvince'])->middleware('cache');
    Route::get('/notifications', [DashboardController::class, 'getNotifications']);
    Route::post('/save-token', [DashboardController::class, 'saveFirebaseToken']);

});


Route::get('/', [DashboardController::class, 'index'])->name('user.dashboard');
Route::get('/sensors/{equipmentTypeId}', [DashboardController::class, 'getSensorsByEquipmentId'])->middleware('cache');

