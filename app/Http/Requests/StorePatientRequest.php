<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class StorePatientRequest extends FormRequest
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
            "full_name" => 'required|max:70|regex:/^[\pL\s]+$/u',
            // "cin" => 'required|unique:patients,cin|alpha_num|size:8',
            'phone' => 'required|unique:patients,phone|numeric|max_digits:10',
            'birth_date' => 'required|date',
            'insurance' => 'string|max:10',
            'gender' => 'required|string',
        ];
    }
  
}
