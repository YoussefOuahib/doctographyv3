<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\GalleryResource;
use App\Http\Resources\UpcomingAppResource;


class AppointmentResource extends JsonResource
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
            'title' => $this->patient->full_name . ' : ' . $this->type,
            'medical_treatment' => $this->medical_treatment,
            'act' => $this->act,
            'type' => $this->type,
            'conditions' => $this->patient->condition,
            'note' => $this->note,
            'patient_id' => $this->patient->id,
            'patient_name' => $this->patient->full_name,
            'patient_cin' => $this->patient->cin,
            'patient_gender' => $this->patient->gender,
            'patient' =>$this->patient,
            'type' => $this->type,
            'next_examination_date' => $this->next_examination_date ? $this->next_examination_date->format('Y-m-d') : null,
            'rate' => $this->rate,
            'date' => $this->created_at->format('Y-m-d'),
            'backgroundColor' => $this->type == 'payment' ? '#68B984' : '#f7a048',
            'total_amount' => $this->total_amount,
            // 'total_sessions' => $this->total_sessions,
            // 'remaining_sessions' => $this->remaining_sessions,
            'remaining_amount' => $this->remaining_amount,
            'images' => GalleryResource::collection($this->gallery)->isEmpty() ? null : GalleryResource::collection($this->gallery),            

        ];
    }
}
