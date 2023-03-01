<?php

namespace App\Models;

use App\Models\Patient;
use App\Models\Gallery;

use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory , Notifiable;
    protected $dates = ['next_examination_date'];


    protected $fillable = [
        'patient_id',
        'act',
        'medical_treatment',
        'next_examination_date',
        'note',
        'rate',
        'type',
        'total_amount',
        // 'total_sessions',
        // 'remaining_sessions',
        'remaining_amount',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    public function scopeStartOfMonth($query) {
        return $query->where('created_at', '>=', Carbon::now()->startOfMonth());
    }
    public function gallery() {
        return $this->hasMany(Gallery::class);
    }
    
  

}
