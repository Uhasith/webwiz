<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Service\Admin\UserManagementService;
use Yajra\DataTables\Facades\DataTables;
use App\Repository\Admin\UserRepo;
use Illuminate\Http\JsonResponse;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    protected $userManagementService;
    protected $userRepository;


    public function __construct(UserRepo $userRepository, UserManagementService $userManagementService)
    {
        $this->userManagementService = $userManagementService;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $roles = Role::all()->pluck('name', 'id');
        $rolesWithPermissions = Role::select('id', 'name')->with('permissions')->orderBy('id')->get();
        $groupedPermissions = Permission::all()->groupBy('category');

        return Inertia::render('Adminview/UserManagement', [
            'rolesWithPermissions' => $rolesWithPermissions,
            'groupedPermissions' => $groupedPermissions,
            'roles' => $roles,
        ]);
    }

    public function getUsersData(Request $request)
    {
        $query = User::with('roles')->select(['id', 'name', 'email', 'contact', 'last_login_at']);

         // Exclude users with the 'superadmin' role
        $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'superadmin');
        });

        if ($request->has('roles')) {
            $roles = $request->get('roles');
            $query->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('name', $roles);
            });
        }

        if ($request->has('search') && !empty($request->get('search'))) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('contact', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        return DataTables::of($query)
            ->addColumn('action', function ($user) {
                return '<button class="btn btn-sm btn-info">Edit</button>';
            })
            ->addColumn('roles', function ($user) {
                return $user->roles->pluck('name')->implode(', ');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getUsers(Request $request)
    {
        $users = $this->userRepository->getUsersWithRoles();

        $data = [
            'draw' => intval($request->draw),
            'recordsTotal' => $users['total'],
            'recordsFiltered' => $users['total'],
            'data' => $users['data'],
        ];

        return response()->json($data);
    }

    public function getRolesWithPermissions(): JsonResponse
    {
        try {
            $roles = $this->userRepository->getRolesWithPermissions();
            return response()->json($roles, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve roles and permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id){
        $data = $this->userRepository->findUserData($id);
        $data->delete();

        return response()->json(['message' => 'Data deleted successfully'], 200);
    }

    public function exportFilteredUsers(Request $request)
    {
        return Excel::download(new UsersExport($request), 'Users.xlsx');
    }
}
