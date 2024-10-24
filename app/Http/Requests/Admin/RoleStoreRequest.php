<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Utility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RoleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        if(Utility::$superAdmin == strtolower(str_replace(' ', '',request()->role_name))){
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role_name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'required|integer|exists:permissions,id',
        ];
    }
}
