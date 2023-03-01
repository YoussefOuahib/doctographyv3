<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'patient_id' => 'required|numeric',
            "act" => 'nullable|string|max:155',
            "medical_treatment" => 'nullable|max:155|string',
            'note' => 'nullable|max:155|string',
            'rate' => 'nullable|numeric',
            'remaining_amount' => 'nullable|numeric',
            'remaining_sessions' => 'nullable|numeric',
            'gallery.*' => 'nullable|image|max:12000'
        ];
    }
}
