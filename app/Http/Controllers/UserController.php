<?php

namespace App\Http\Controllers;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\ModelsV2\UserOrganization;
use App\Service\ForgetPasswordService;
use App\Service\User\EmailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        'organization_id'=> 'required',
        'username' => 'required|string|max:255',
        'email' => 'required|email|max:255',
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
        'email'=> $user->email,
        'password' => $password,
    ];

    if($user){
        $userOrganization = UserOrganization::create([
            'user_id' => $user->id,
            'organization_id' => $data['organization_id'],
            'status' => Utility::$statusActive,
        ]);

        $user->roles()->attach($data['role_id']);
        EmailService::registerEmail($emailData);
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }else{
        return response()->json(['message' => 'User not created',  $data ], 500);
    }

}

// Get User Data by ID
public function edit($id)
{
    $user = User::with('roles')->findOrFail($id);
    return response()->json($user);
}

// Update User Data
public function update(Request $request, $id)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'contact' => 'required|string|max:15',
        'status' => 'required|string',
        'role_id' => 'required',
    ]);

    $user = User::findOrFail($id);

    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->status = $data['status'];
    $user->contact = $data['contact'];
    $user->save();

    $user->roles()->sync($data['role_id']);

    return response()->json(['message' => 'User updated successfully.']);
}



    public function forgetPassword(Request $request){

        $email = $request->email;
        Utility::log(['email'=>$email],get_class());

        $user =  $this->forgetPasswordService->sendOtp($email);
        return response()->json(['message'=>'The OTP is sent to your email'],201);
    }

    public function resetPassword(Request $request){

        $newPassword = $request->password;
        $confirmPassword = $request->password_confirmation;
        $otp = $request->otp;
        $email = $request->email;

        Utility::log(['email'=>$email],get_class());
        $resetPassword = $this->forgetPasswordService->resetPassword($confirmPassword,$newPassword,$otp,$email);
        return response()->json(['message'=>'Password is Successfully resetted'],201);
    }
}
