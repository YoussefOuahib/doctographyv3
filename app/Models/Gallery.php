<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;

class Gallery extends Model
{
    use HasFactory;

    public $fillable = [
        'image',
        'appointment_id',
    ];
 
    public function appointment() {
        return $this->belongsTo(Appointment::class);
    }

}
