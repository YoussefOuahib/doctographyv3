<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpcomingAppointment extends Model
{
    use HasFactory;
    protected $dates = ['date'];

    protected $fillable = [
        'name',
        'date',
    ];
}
