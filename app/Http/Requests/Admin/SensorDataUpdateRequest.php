<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SensorDataUpdateRequest extends FormRequest
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
            'pm2_5' => 'nullable|numeric|min:0',
            'pm10' => 'nullable|numeric|min:0',
            'o3' => 'nullable|numeric|min:0',
            'co' => 'nullable|numeric|min:0',
            'no2' => 'nullable|numeric|min:0',
            'so2' => 'nullable|numeric|min:0',
            'co2' => 'nullable|numeric|min:0',
            'sensor_data_id' => 'required|exists:sensor_datas,id',
        ];
    }
}
