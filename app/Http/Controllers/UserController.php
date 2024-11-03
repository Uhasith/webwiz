<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Utility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ModelsV2\UserOrganization;
use App\Service\User\EmailService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Service\ForgetPasswordService;

class UserController extends Controller
{
    private $forgetPasswordService;


    public function __construct(ForgetPasswordService  $forgetPasswordService)
    {
        $this->forgetPasswordService =  $forgetPasswordService;
    }


    public function store(Request $request)
    {
        $password = str()->random(6);

        $data = $request->validate([
            'organization_id' => 'required',
            'username' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|max:255|unique:users,email',
            'contact' => 'required|string|max:15',
            'role_id' => 'required',
        ]);

        // Call the repository to save the user
        $user = User::create([
            'name' => $data['username'],
            'email' => $data['email'],
            'contact' => $data['contact'],
            'password' => Hash::make($password),
        ]);

        $emailData = [
            'username' => $user->name,
            'email' => $user->email,
            'password' => $password,
        ];

        if ($user) {
            UserOrganization::create([
                'user_id' => $user->id,
                'organization_id' => $data['organization_id'],
                'status' => Utility::$statusActive,
            ]);

            $role = Role::find($data['role_id']);
            $user->assignRole($role);

            EmailService::registerEmail($emailData);
            return response()->json(['message' => 'User created successfully', 'user' => $user], 200);
        } else {
            return response()->json(['message' => 'Failed to create user',  $data], 500);
        }
    }

    // Get User Data by ID
    public function edit($id)
    {
        $user = User::with('roles', 'userOrganizations')->findOrFail($id);
        return response()->json($user);
    }

    // Update User Data
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'organization_id' => 'required',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'contact' => 'required|string|max:15',
            'role_id' => 'required',
        ]);

        $user = User::findOrFail($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

        $updateData = [
            'name' => $data['username'],
            'email' => $data['email'],
            'contact' => $data['contact'],
        ];

        $user->update($updateData);

        $user->syncRoles([]);
        $role = Role::find($data['role_id']);
        $user->assignRole($role);

        $user->userOrganizations()->delete();
        UserOrganization::create([
            'user_id' => $user->id,
            'organization_id' => $data['organization_id'],
            'status' => Utility::$statusActive,
        ]);

        $user->refresh();

        return response()->json(['message' => 'User updated successfully.', 'user' => $user], 200);

    }



    public function forgetPassword(Request $request)
    {

        $email = $request->email;
        Utility::log(['email' => $email], get_class());

        $user =  $this->forgetPasswordService->sendOtp($email);
        return response()->json(['message' => 'The OTP is sent to your email'], 201);
    }

    public function resetPassword(Request $request)
    {

        $newPassword = $request->password;
        $confirmPassword = $request->password_confirmation;
        $otp = $request->otp;
        $email = $request->email;

        Utility::log(['email' => $email], get_class());
        $resetPassword = $this->forgetPasswordService->resetPassword($confirmPassword, $newPassword, $otp, $email);
        return response()->json(['message' => 'Password is Successfully resetted'], 201);
    }
}
