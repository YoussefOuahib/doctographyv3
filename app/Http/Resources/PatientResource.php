<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ConditionResource;

use Carbon\Carbon;


class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'cin' => $this->cin,
            'age' => $this->birth_date->diff(Carbon::now())->format('%y'),
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'first_app' => $this->created_at->format('d M Y'),
            'insurance' => $this->insurance,
            'appointments' =>  AppointmentResource::collection($this->appointments),

        ];
    }
}
