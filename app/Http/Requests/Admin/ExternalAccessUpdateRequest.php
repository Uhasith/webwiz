<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ExternalAccessUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'name' => 'required|string|max:255',
            'whitelisted_ips' => 'nullable|array',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:locations,id',
            'sensors' => 'required|array|min:1',
            'sensors.*' => 'exists:sensors,id',
            'recent_data' => 'nullable|boolean',
            'hourly_data' => 'nullable|boolean',
            'daily_data' => 'nullable|boolean',
            'monthly_data' => 'nullable|boolean',
            'annually_data' => 'nullable|boolean',
            'status' => 'string|in:active,inactive',
        ];
    }
}
