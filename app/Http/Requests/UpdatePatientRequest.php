<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
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
            "full_name" => 'required|regex:/^[\pL\s]+$/u|max:70',
            "cin" => 'required|alpha_num|size:8',
            'phone' => 'required|numeric|max_digits:10',
            'birth_date' => 'required|date',
            'insurance' => 'string|max:5',
            'gender' => 'required|string',
        ];
    }
}
