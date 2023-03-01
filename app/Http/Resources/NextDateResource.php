<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NextDateResource extends JsonResource
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
            'title' => $this->patient->full_name . ':' . $this->type,
            'patient_id' => $this->patient->id,
            'date' => $this->next_examination_date->format('Y-m-d'),
            'backgroundColor' => '#2196F3',
            'next_date' => true,
        ];
    }
}
